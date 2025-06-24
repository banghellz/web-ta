<?php

namespace App\Http\Controllers;

use App\Models\RfidTag;
use App\Models\UserDetail;
use App\Models\User;
use App\Models\Notification;
use App\Mail\AccountRegistrationSuccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class CompleteProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $extractedNim = session('extracted_nim');

        // Get user's photo - try to get from Google first, then use default
        $userPhoto = $this->getUserPhotoUrl($user);

        return view('user.complete-profile', compact('user', 'extractedNim', 'userPhoto'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // Validasi form
        $request->validate([
            'nim' => 'required|integer|min:0|unique:user_details,nim',
            'no_koin' => 'required|numeric|digits:4', // Changed to require exactly 4 digits
            'prodi' => 'required|string|max:50',
            'pict' => 'nullable|image|mimes:jpg,jpeg,png|max:1024', // Changed to 1MB (1024 KB)
        ], [
            'pict.max' => 'Ukuran file gambar tidak boleh lebih dari 1MB.',
            'pict.image' => 'File yang diupload harus berupa gambar.',
            'pict.mimes' => 'Format gambar harus JPG, JPEG, atau PNG.',
        ]);

        try {
            $namaFileFoto = null;

            // Handle profile picture
            if ($request->hasFile('pict')) {
                // User uploaded a custom photo
                $namaFileFoto = $this->handleFileUpload($request->file('pict'));
            } else {
                // Use Google photo as default
                $namaFileFoto = $this->saveGooglePhoto($user);
            }

            DB::beginTransaction();

            // Format no_koin to ensure it has leading zeros
            $noKoin = str_pad($request->no_koin, 4, '0', STR_PAD_LEFT);

            // Find available RFID tag
            $rfidTag = RfidTag::getAvailableTag();
            $rfidUid = null;

            if ($rfidTag) {
                $rfidUid = $rfidTag->uid;
                $rfidTag->markAsUsed();
            }

            // Save to user_detail relation
            $user->detail()->create([
                'nama' => $user->name,
                'nim' => $request->nim,
                'no_koin' => $noKoin,
                'prodi' => $request->prodi,
                'pict' => $namaFileFoto,
                'rfid_uid' => $rfidUid,
            ]);

            DB::commit();

            // Remove NIM data from session
            session()->forget('extracted_nim');

            // Send notification email
            try {
                $user->refresh();
                $user->load('detail');
                Notification::userRegistered($user);
                Mail::to($user->email)->send(new AccountRegistrationSuccess($user));

                Log::info('Registration success email sent to: ' . $user->email);
                session()->flash('email_sent', 'Email konfirmasi telah dikirim ke alamat email Anda.');
            } catch (\Exception $emailException) {
                Log::error('Failed to send registration email to: ' . $user->email . '. Error: ' . $emailException->getMessage());
                session()->flash('email_failed', 'Registrasi berhasil, tetapi gagal mengirim email konfirmasi. Silakan hubungi admin jika diperlukan.');
            }

            // Redirect based on user role
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard.index')->with('success', 'Profil berhasil dilengkapi!'),
                default => redirect()->route('user.dashboard.index')->with('success', 'Profil berhasil dilengkapi!'),
            };
        } catch (QueryException $e) {
            DB::rollBack();
            $this->cleanupUploadedFile($namaFileFoto);

            if ($e->errorInfo[1] == 1062) {
                return back()
                    ->withInput()
                    ->withErrors(['nim' => 'NIM sudah digunakan oleh pengguna lain.']);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan database: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->cleanupUploadedFile($namaFileFoto);

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get user's photo URL - try Google/Gravatar first, then default
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

        return asset('images/default-avatar.png');
    }

    /**
     * Download and save photo from URL
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
     * Save user photo - either from upload or from email/gravatar
     */
    private function saveGooglePhoto($user)
    {
        try {
            // Get user photo URL
            $photoUrl = $this->getUserPhotoUrl($user);

            // If we have a valid photo URL (not default), try to download it
            if ($photoUrl && $photoUrl !== asset('images/default-avatar.png')) {
                $filename = $this->downloadAndSavePhoto($photoUrl, 'email');
                if ($filename) {
                    return $filename;
                }
            }

            // If can't download photo, copy default avatar
            $uploadPath = public_path('profile_pictures');
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            $defaultAvatar = public_path('images/default-avatar.png');
            if (File::exists($defaultAvatar)) {
                $filename = 'default_' . uniqid() . '.png';
                $filepath = $uploadPath . '/' . $filename;
                File::copy($defaultAvatar, $filepath);
                return $filename;
            }
        } catch (\Exception $e) {
            Log::warning('Could not save user photo: ' . $e->getMessage());
        }

        return 'default-avatar.png';
    }

    /**
     * Handle file upload with size validation
     */
    private function handleFileUpload($file)
    {
        $uploadPath = public_path('profile_pictures');

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        if (!is_writable($uploadPath)) {
            throw new \Exception('Direktori upload tidak dapat ditulis. Silakan hubungi administrator.');
        }

        // Additional size check (1MB = 1024 * 1024 bytes)
        $maxSizeInBytes = 1024 * 1024; // 1MB in bytes
        if ($file->getSize() > $maxSizeInBytes) {
            throw new \Exception('Ukuran file gambar tidak boleh lebih dari 1MB.');
        }

        $filename = uniqid() . '.' . $file->getClientOriginalExtension();

        if (!$file->move($uploadPath, $filename)) {
            throw new \Exception('Gagal mengupload file gambar.');
        }

        return $filename;
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
