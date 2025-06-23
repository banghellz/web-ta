<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use App\Models\RfidTag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index()
    {
        $user = Auth::user();
        $user->load('detail');

        $availableRfidTags = RfidTag::where(function ($query) use ($user) {
            $query->where('status', 'Available')
                ->orWhere('uid', $user->detail->rfid_uid ?? '');
        })->orderBy('uid')->get();

        return view('user.profile.index', compact('user', 'availableRfidTags'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validation rules
        $rules = [
            'no_koin' => 'nullable|string|max:3|regex:/^[0-9]{1,3}$/',
            'prodi' => 'nullable|string|max:50',
            'pict' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',
        ];

        // Add unique validation for no_koin excluding current user
        if ($user->detail) {
            $rules['no_koin'] .= '|unique:user_details,no_koin,' . $user->detail->id;
        } else {
            $rules['no_koin'] .= '|unique:user_details,no_koin';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            // Handle profile picture upload
            $pictureFileName = null;
            if ($request->hasFile('pict')) {
                $pictureFileName = $this->handleFileUpload($request->file('pict'));

                // Delete old picture if exists
                if ($user->detail && $user->detail->pict && $user->detail->pict !== 'default-avatar.png') {
                    $this->cleanupUploadedFile($user->detail->pict);
                }
            }

            // Format no_koin - PENTING: Simpan sebagai INTEGER dengan prefix 0, bukan string dengan α
            $formattedNoKoin = null;
            if ($request->filled('no_koin')) {
                // Remove any non-digit characters
                $cleanNoKoin = preg_replace('/[^0-9]/', '', $request->no_koin);
                if (!empty($cleanNoKoin) && is_numeric($cleanNoKoin)) {
                    // Pad to 4 digits dengan prefix 0 (disimpan sebagai integer)
                    // Contoh: input 112 -> disimpan sebagai 0112 (integer)
                    $formattedNoKoin = (int) str_pad($cleanNoKoin, 4, '0', STR_PAD_LEFT);
                }
            }

            // Prepare data for update/create
            $detailData = [
                'nama' => $user->name,
                'prodi' => $request->prodi,
                // RFID remains unchanged - only admin can modify
            ];

            // Add no_koin hanya jika ada value
            if ($formattedNoKoin !== null) {
                $detailData['no_koin'] = $formattedNoKoin;
            }

            // Add picture filename if uploaded
            if ($pictureFileName) {
                $detailData['pict'] = $pictureFileName;
            }

            if ($user->detail) {
                // Update existing details
                $user->detail->update($detailData);
                $updatedDetail = $user->detail->fresh();
            } else {
                // Create new details
                $detailData['user_id'] = $user->id;
                $detailData['nim'] = null;
                $detailData['rfid_uid'] = null;
                $detailData['koin'] = 10;
                $updatedDetail = $user->detail()->create($detailData);
            }

            DB::commit();

            // Reload user with fresh data
            $user->load('detail');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully!',
                    'data' => [
                        'no_koin' => $updatedDetail->no_koin,
                        'no_koin_display' => $updatedDetail->no_koin ? substr(str_pad($updatedDetail->no_koin, 4, '0', STR_PAD_LEFT), 1) : '', // Remove leading 0 for display
                        'prodi' => $updatedDetail->prodi,
                        'pict_url' => $updatedDetail->pict ? asset('profile_pictures/' . $updatedDetail->pict) : null
                    ]
                ]);
            }

            return redirect()->route('user.profile.index')->with('success', 'Profile updated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            if ($pictureFileName) {
                $this->cleanupUploadedFile($pictureFileName);
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }

            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();

            if ($pictureFileName) {
                $this->cleanupUploadedFile($pictureFileName);
            }

            Log::error('Profile update error: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating your profile.'
                ], 500);
            }

            return back()->withInput()->withErrors(['error' => 'An error occurred while updating your profile.']);
        }
    }

    /**
     * Get available RFID tags (AJAX endpoint)
     */
    public function getAvailableRfidTags()
    {
        try {
            $user = Auth::user();

            $availableRfidTags = RfidTag::where(function ($query) use ($user) {
                $query->where('status', 'Available')
                    ->orWhere('uid', $user->detail->rfid_uid ?? '');
            })->orderBy('uid')->get();

            return response()->json([
                'success' => true,
                'data' => $availableRfidTags
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching RFID tags: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch RFID tags'
            ], 500);
        }
    }

    /**
     * Handle file upload
     */
    private function handleFileUpload($file)
    {
        $uploadPath = public_path('profile_pictures');

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        if (!is_writable($uploadPath)) {
            throw new \Exception('Upload directory is not writable. Please contact administrator.');
        }

        // Validate file
        if (!$file->isValid()) {
            throw new \Exception('Invalid file upload.');
        }

        // Check file size (10MB = 10485760 bytes)
        if ($file->getSize() > 10485760) {
            throw new \Exception('File size exceeds maximum limit of 10MB.');
        }

        // Check file type
        $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new \Exception('Invalid file type. Only JPG, JPEG, and PNG files are allowed.');
        }

        // Generate filename dengan uniqid seperti CompleteProfileController
        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        if (!$file->move($uploadPath, $filename)) {
            throw new \Exception('Failed to upload profile picture.');
        }

        return $filename;
    }

    /**
     * Clean up uploaded file on error
     */
    private function cleanupUploadedFile($filename)
    {
        if ($filename && $filename !== 'default-avatar.png' && !str_starts_with($filename, 'default_')) {
            $filepath = public_path('profile_pictures/' . $filename);
            if (File::exists($filepath)) {
                try {
                    File::delete($filepath);
                    Log::info('Deleted old/error profile picture: ' . $filename);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete profile picture: ' . $e->getMessage());
                }
            }
        }
    }
}
