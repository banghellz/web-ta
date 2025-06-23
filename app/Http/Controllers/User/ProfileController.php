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

        try {
            // Validation rules dengan pesan custom
            $rules = [
                'no_koin' => 'nullable|string|max:3|regex:/^[0-9]{1,3}$/',
                'prodi' => 'nullable|string|max:50',
                'pict' => [
                    'nullable',
                    'file',
                    'mimes:jpg,jpeg,png',
                    'max:1024', // 1MB dalam KB
                    function ($attribute, $value, $fail) {
                        if ($value) {
                            // Additional size check in bytes (1MB = 1048576 bytes)
                            if ($value->getSize() > 3145728) {
                                $fail('The ' . $attribute . ' must not be greater than 1MB.');
                            }

                            // Check if file is actually an image
                            $imageInfo = @getimagesize($value->getPathname());
                            if (!$imageInfo) {
                                $fail('The ' . $attribute . ' must be a valid image file.');
                            }
                        }
                    }
                ],
            ];

            // Add unique validation for no_koin excluding current user
            if ($user->detail) {
                $rules['no_koin'] .= '|unique:user_details,no_koin,' . $user->detail->id;
            } else {
                $rules['no_koin'] .= '|unique:user_details,no_koin';
            }

            $validatedData = $request->validate($rules, [
                'no_koin.regex' => 'Coin number must contain only digits (1-3 characters).',
                'no_koin.unique' => 'This coin number is already taken by another user.',
                'pict.mimes' => 'Profile picture must be a JPG, JPEG, or PNG file.',
                'pict.max' => 'Profile picture must not be larger than 1MB.',
            ]);

            DB::beginTransaction();

            // Handle profile picture upload
            $pictureFileName = null;
            if ($request->hasFile('pict')) {
                Log::info('Processing file upload', [
                    'original_name' => $request->file('pict')->getClientOriginalName(),
                    'size' => $request->file('pict')->getSize(),
                    'mime_type' => $request->file('pict')->getMimeType()
                ]);

                $pictureFileName = $this->handleFileUpload($request->file('pict'));

                // Delete old picture if exists
                if ($user->detail && $user->detail->pict && $user->detail->pict !== 'default-avatar.png') {
                    $this->cleanupUploadedFile($user->detail->pict);
                }

                Log::info('File uploaded successfully', ['filename' => $pictureFileName]);
            }

            // Format no_koin - simpan sebagai INTEGER dengan padding 0
            $formattedNoKoin = null;
            if ($request->filled('no_koin')) {
                $cleanNoKoin = preg_replace('/[^0-9]/', '', $request->no_koin);
                if (!empty($cleanNoKoin) && is_numeric($cleanNoKoin)) {
                    // Pad to 4 digits dengan prefix 0 (disimpan sebagai integer)
                    $formattedNoKoin = (int) str_pad($cleanNoKoin, 4, '0', STR_PAD_LEFT);
                }
            }

            // Prepare data for update/create
            $detailData = [
                'nama' => $user->name,
                'prodi' => $validatedData['prodi'] ?? null,
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

            Log::info('Profile update completed successfully', ['user_id' => $user->id]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully!',
                    'data' => [
                        'no_koin' => $updatedDetail->no_koin,
                        'no_koin_display' => $updatedDetail->no_koin ? substr(str_pad($updatedDetail->no_koin, 4, '0', STR_PAD_LEFT), 1) : '',
                        'prodi' => $updatedDetail->prodi,
                        'pict_url' => $updatedDetail->pict ? asset('profile_pictures/' . $updatedDetail->pict) : null
                    ]
                ]);
            }

            // Tidak menggunakan flash message, hanya redirect
            return redirect()->route('user.profile.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();

            Log::warning('Profile update validation failed', [
                'user_id' => $user->id,
                'errors' => $e->errors()
            ]);

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

            // Clean up uploaded file if there was an error
            if (isset($pictureFileName) && $pictureFileName) {
                $this->cleanupUploadedFile($pictureFileName);
            }

            Log::error('Profile update error', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating your profile: ' . $e->getMessage()
                ], 500);
            }

            return back()->withInput()->withErrors(['error' => 'An error occurred while updating your profile: ' . $e->getMessage()]);
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
     * Handle file upload with better size and validation handling
     */
    private function handleFileUpload($file)
    {
        $uploadPath = public_path('profile_pictures');

        // Ensure upload directory exists
        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        // Check if directory is writable
        if (!is_writable($uploadPath)) {
            throw new \Exception('Upload directory is not writable. Please contact administrator.');
        }

        // Validate file
        if (!$file->isValid()) {
            throw new \Exception('Invalid file upload.');
        }

        // Double check file size (1MB = 1048576 bytes)
        if ($file->getSize() > 1048576) {
            throw new \Exception('File size exceeds maximum limit of 1MB. Current size: ' . round($file->getSize() / 1048576, 2) . 'MB');
        }

        // Validate file is actually an image
        $imageInfo = @getimagesize($file->getPathname());
        if (!$imageInfo) {
            throw new \Exception('File is not a valid image.');
        }

        // Check MIME type
        $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new \Exception('Invalid file type. Only JPG, JPEG, and PNG files are allowed. Detected: ' . $file->getMimeType());
        }

        // Generate unique filename with timestamp untuk menghindari konflik
        $extension = $file->getClientOriginalExtension();
        $filename = 'profile_' . time() . '_' . uniqid() . '.' . $extension;

        // Move file to upload directory
        if (!$file->move($uploadPath, $filename)) {
            throw new \Exception('Failed to upload profile picture.');
        }

        // Verify file was uploaded correctly
        $uploadedFile = $uploadPath . '/' . $filename;
        if (!File::exists($uploadedFile)) {
            throw new \Exception('File upload verification failed.');
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
