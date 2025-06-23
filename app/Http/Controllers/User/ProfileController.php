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
use Illuminate\Support\Facades\Http;
use Illuminate\Database\QueryException;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function index()
    {
        $user = Auth::user();
        $user->load('detail'); // Eager load detail relationship

        // Get available RFID tags (Available status OR current user's tag)
        $availableRfidTags = RfidTag::where(function ($query) use ($user) {
            $query->where('status', 'Available')
                ->orWhere('uid', $user->detail->rfid_uid ?? '');
        })->orderBy('uid')->get();

        return view('user.profile.index', compact('user', 'availableRfidTags'));
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
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validation rules - menggunakan pattern seperti CompleteProfileController
        $rules = [
            'no_koin' => 'nullable|string|max:3|regex:/^[0-9]{1,3}$/', // 1-3 digits only
            'prodi' => 'nullable|string|max:50',
            'pict' => 'nullable|image|mimes:jpg,jpeg,png|max:10240', // 10MB max
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

            // Handle profile picture upload menggunakan metode dari CompleteProfileController
            $namaFileFoto = null;
            if ($request->hasFile('pict')) {
                $namaFileFoto = $this->handleFileUpload($request->file('pict'));

                // Delete old picture if exists
                if ($user->detail && $user->detail->pict && $user->detail->pict !== 'default-avatar.png') {
                    $this->cleanupUploadedFile($user->detail->pict);
                }
            }

            // Format no_koin menggunakan prefix alpha seperti permintaan
            $formattedNoKoin = null;
            if ($request->filled('no_koin')) {
                // Remove any non-digit characters
                $cleanNoKoin = preg_replace('/[^0-9]/', '', $request->no_koin);
                if (!empty($cleanNoKoin) && is_numeric($cleanNoKoin)) {
                    // Pad to 3 digits and add alpha prefix
                    $paddedNumber = str_pad($cleanNoKoin, 3, '0', STR_PAD_LEFT);
                    $formattedNoKoin = 'α' . $paddedNumber;
                }
            } else {
                // Keep existing no_koin if not provided
                $formattedNoKoin = $user->detail->no_koin ?? null;
            }

            // Prepare data for update/create
            $detailData = [
                'nama' => $user->name, // Keep name synchronized
                'no_koin' => $formattedNoKoin,
                'prodi' => $request->prodi,
                // RFID remains unchanged - only admin can modify
            ];

            // Add picture filename if uploaded
            if ($namaFileFoto) {
                $detailData['pict'] = $namaFileFoto;
            }

            if ($user->detail) {
                // Update existing details
                $user->detail->update($detailData);
                $updatedDetail = $user->detail->fresh();
            } else {
                // Create new details
                $detailData['user_id'] = $user->id;
                $detailData['nim'] = null; // NIM can only be set during registration
                $detailData['rfid_uid'] = null; // RFID assigned by admin
                $detailData['koin'] = 10; // Default koin value
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
                        'prodi' => $updatedDetail->prodi,
                        'pict_url' => $updatedDetail->pict ? asset('profile_pictures/' . $updatedDetail->pict) : null
                    ]
                ]);
            }

            return redirect()->route('user.profile.index')->with('success', 'Profile updated successfully!');
        } catch (QueryException $e) {
            DB::rollBack();

            // Clean up uploaded file if there was an error
            if ($namaFileFoto) {
                $this->cleanupUploadedFile($namaFileFoto);
            }

            $errorMessage = 'Database error occurred while updating profile.';

            if ($e->errorInfo[1] == 1062) { // Duplicate entry error
                if (strpos($e->getMessage(), 'no_koin') !== false) {
                    $errorMessage = 'The coin number is already taken by another user.';
                }
            }

            Log::error('Database error updating profile: ' . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 422);
            }

            return back()->withInput()->withErrors(['error' => $errorMessage]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up uploaded file if there was an error
            if ($namaFileFoto) {
                $this->cleanupUploadedFile($namaFileFoto);
            }

            Log::error('Profile update error: ' . $e->getMessage());

            $errorMessage = 'An error occurred while updating your profile. Please try again.';

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 500);
            }

            return back()->withInput()->withErrors(['error' => $errorMessage]);
        }
    }

    /**
     * Handle file upload - menggunakan metode yang sama dengan CompleteProfileController
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
     * Clean up uploaded file on error - sama dengan CompleteProfileController
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

    /**
     * Get user's photo URL - borrowed from CompleteProfileController
     */
    private function getUserPhotoUrl($user)
    {
        try {
            $email = $user->email;

            // Try to get Gravatar (which often includes Google photos)
            if (strpos($email, '@') !== false) {
                $hash = md5(strtolower(trim($email)));
                $gravatarUrl = "https://www.gravatar.com/avatar/{$hash}?s=200&d=404";

                // Check if Gravatar exists by making a simple request
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 5,
                        'method' => 'HEAD'
                    ]
                ]);

                $headers = @get_headers($gravatarUrl, 0, $context);
                if ($headers && strpos($headers[0], '200') !== false) {
                    return $gravatarUrl;
                }

                // Try alternative: get Gravatar with default fallback to a specific image
                $gravatarUrlWithDefault = "https://www.gravatar.com/avatar/{$hash}?s=200&d=mp";
                return $gravatarUrlWithDefault;
            }
        } catch (\Exception $e) {
            Log::warning('Could not fetch user photo: ' . $e->getMessage());
        }

        return asset('assets/img/default-avatar.png');
    }

    /**
     * Download and save photo from URL - borrowed from CompleteProfileController
     */
    private function downloadAndSavePhoto($photoUrl, $prefix = 'profile')
    {
        try {
            $uploadPath = public_path('profile_pictures');

            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            $response = Http::timeout(10)->get($photoUrl);

            if ($response->successful()) {
                $extension = 'jpg'; // Default to jpg

                // Try to get extension from content type
                $contentType = $response->header('Content-Type');
                if (strpos($contentType, 'png') !== false) {
                    $extension = 'png';
                } elseif (strpos($contentType, 'jpeg') !== false || strpos($contentType, 'jpg') !== false) {
                    $extension = 'jpg';
                }

                $filename = $prefix . '_' . uniqid() . '.' . $extension;
                $filepath = $uploadPath . '/' . $filename;

                File::put($filepath, $response->body());
                return $filename;
            }
        } catch (\Exception $e) {
            Log::warning('Could not download photo from URL: ' . $e->getMessage());
        }

        return null;
    }
}
