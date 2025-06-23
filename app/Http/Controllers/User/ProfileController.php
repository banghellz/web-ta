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

        // Validation rules - no_koin should accept 1-3 digits since user inputs without leading zero
        $rules = [
            'no_koin' => 'nullable|numeric|min:1|max:999', // Accept 1-3 digits
            'prodi' => 'nullable|string|max:50',
            'pict' => 'nullable|image|mimes:jpg,jpeg,png|max:10240', // 10MB max
        ];

        // If user has details, add unique validation for no_koin (excluding current user)
        if ($user->detail) {
            $rules['no_koin'] = 'nullable|numeric|min:1|max:999|unique:user_details,no_koin,' . $user->detail->id;
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            // Handle profile picture upload
            $pictureFileName = null;
            if ($request->hasFile('pict')) {
                $pictureFileName = $this->handleFileUpload($request->file('pict'));

                // Delete old picture if exists
                if ($user->detail && $user->detail->pict) {
                    $this->deleteOldPicture($user->detail->pict);
                }
            }

            // Format no_koin - pad to 4 digits with leading zero
            $formattedNoKoin = null;
            if ($request->filled('no_koin')) {
                $formattedNoKoin = str_pad($request->no_koin, 4, '0', STR_PAD_LEFT);
            }

            // Update or create user details
            $detailData = [
                'nama' => $user->name, // Keep name synchronized
                'no_koin' => $formattedNoKoin,
                'prodi' => $request->prodi,
                // RFID remains unchanged - only admin can modify
            ];

            // Add picture filename if uploaded
            if ($pictureFileName) {
                $detailData['pict'] = $pictureFileName;
            }

            if ($user->detail) {
                // Update existing details
                $user->detail->update($detailData);
            } else {
                // Create new details
                $detailData['user_id'] = $user->id;
                $detailData['nim'] = null; // NIM can only be set during registration
                $detailData['rfid_uid'] = null; // RFID assigned by admin
                $user->detail()->create($detailData);
            }

            DB::commit();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profile updated successfully!',
                    'redirect' => route('user.profile.index')
                ]);
            }

            return redirect()->route('user.profile.index')->with('success', 'Profile updated successfully!');
        } catch (QueryException $e) {
            DB::rollBack();

            // Clean up uploaded file if there was an error
            if ($pictureFileName) {
                $this->cleanupUploadedFile($pictureFileName);
            }

            $errorMessage = 'Database error occurred while updating profile.';

            if ($e->errorInfo[1] == 1062) { // Duplicate entry error
                if (strpos($e->getMessage(), 'no_koin') !== false) {
                    $errorMessage = 'The coin number is already taken by another user.';
                }
            }

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
            if ($pictureFileName) {
                $this->cleanupUploadedFile($pictureFileName);
            }

            Log::error('Profile update error: ' . $e->getMessage());

            $errorMessage = 'An error occurred while updating your profile: ' . $e->getMessage();

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
     * Get user's photo URL - try Google/Gravatar first, then default
     * (Borrowed from CompleteProfileController)
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
     * Download and save photo from URL
     * (Borrowed from CompleteProfileController)
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

    /**
     * Handle file upload with improved error handling
     * (Improved version from CompleteProfileController)
     */
    private function handleFileUpload($file)
    {
        try {
            $uploadPath = public_path('profile_pictures');

            // Create directory if it doesn't exist
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

            // Check file size (10MB = 10485760 bytes)
            if ($file->getSize() > 10485760) {
                throw new \Exception('File size exceeds maximum limit of 10MB.');
            }

            // Check file type
            $allowedMimeTypes = ['image/jpeg', 'image/jpg', 'image/png'];
            if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
                throw new \Exception('Invalid file type. Only JPG, JPEG, and PNG files are allowed.');
            }

            // Generate unique filename
            $extension = $file->getClientOriginalExtension();
            $filename = 'profile_' . uniqid() . '.' . $extension;

            // Move file
            if (!$file->move($uploadPath, $filename)) {
                throw new \Exception('Failed to upload profile picture.');
            }

            return $filename;
        } catch (\Exception $e) {
            Log::error('File upload error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete old profile picture
     */
    private function deleteOldPicture($filename)
    {
        if ($filename && $filename !== 'default-avatar.png' && !str_starts_with($filename, 'default_')) {
            $filepath = public_path('profile_pictures/' . $filename);
            if (File::exists($filepath)) {
                try {
                    File::delete($filepath);
                    Log::info('Deleted old profile picture: ' . $filename);
                } catch (\Exception $e) {
                    Log::warning('Failed to delete old profile picture: ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * Clean up uploaded file on error
     */
    private function cleanupUploadedFile($filename)
    {
        if ($filename) {
            $filepath = public_path('profile_pictures/' . $filename);
            if (File::exists($filepath)) {
                try {
                    File::delete($filepath);
                    Log::info('Cleaned up uploaded file after error: ' . $filename);
                } catch (\Exception $e) {
                    Log::warning('Failed to cleanup uploaded file: ' . $e->getMessage());
                }
            }
        }
    }
}
