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

        return view('user.profile.index', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validation rules (removed rfid_uid since users can't edit it)
        $rules = [
            'no_koin' => 'nullable|numeric|digits:4',
            'prodi' => 'nullable|string|max:50',
            'pict' => 'nullable|image|mimes:jpg,jpeg,png|max:10240', // 10MB max
        ];

        // If user has details, add unique validation for no_koin (excluding current user)
        if ($user->detail) {
            $rules['no_koin'] = 'nullable|numeric|digits:4|unique:user_details,no_koin,' . $user->detail->id;
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

            // Update or create user details (removed RFID handling)
            $detailData = [
                'nama' => $user->name, // Keep name synchronized
                'no_koin' => $request->no_koin ? str_pad($request->no_koin, 4, '0', STR_PAD_LEFT) : null,
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

        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        if (!$file->move($uploadPath, $filename)) {
            throw new \Exception('Failed to upload profile picture.');
        }

        return $filename;
    }

    /**
     * Delete old profile picture
     */
    private function deleteOldPicture($filename)
    {
        if ($filename && $filename !== 'default-avatar.png') {
            $filepath = public_path('profile_pictures/' . $filename);
            if (File::exists($filepath)) {
                File::delete($filepath);
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
                File::delete($filepath);
            }
        }
    }
}
