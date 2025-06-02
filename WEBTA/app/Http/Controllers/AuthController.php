<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $email = strtolower($googleUser->email);

            $user = User::firstOrNew(['email' => $email]);
            $isNewUser = !$user->exists;

            if ($isNewUser) {
                $user->uuid = Str::uuid();
                $user->role = 'user'; // Default role
            }

            $user->name = $googleUser->name;
            $user->password = bcrypt(Str::random(16));

            // Ekstrak NIM dari email
            $nim = $this->extractNimFromEmail($email);

            $user->save();

            Auth::login($user);

            // Log the activity - metode yang sudah disederhanakan
            $this->logActivity($user, $isNewUser ? 'register' : 'login');

            // Kalau belum isi data lengkap, redirect dulu ke halaman isi data
            if (!$user->detail) {
                // Jika NIM berhasil diekstrak, tambahkan ke session untuk digunakan di form
                if ($nim) {
                    session(['extracted_nim' => $nim]);
                }
                return redirect()->route('user.complete-profile');
            }

            // Redirect berdasarkan role dengan nama route
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard.index'),
                default => redirect()->route('user.dashboard'),
            };
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            return redirect('/login')->with('error', 'Session expired, please try again');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Login gagal: ' . $e->getMessage());
        }
    }
    /**
     * Ekstrak NIM dari email mahasiswa
     * Format email: nama.nim@domain.com
     * 
     * @param string $email
     * @return string|null
     */
    private function extractNimFromEmail($email)
    {
        // Pattern untuk mencari NIM di bagian nama email
        // Asumsi format email adalah nama.20222007@domain.com
        if (preg_match('/\.(\d{8})@/', $email, $matches)) {
            return $matches[1]; // Mengambil grup yang cocok (8 digit angka)
        }
        return null; // Jika tidak ditemukan
    }
    public function logout(Request $request)
    {
        // Log the logout activity before logging out
        if (Auth::check()) {
            $this->logActivity(Auth::user(), 'logout');
        }

        Auth::logout();
        return redirect('/');
    }

    /**
     * Log user activity
     * 
     * @param User $user
     * @param string $activity
     * @return void
     */
    protected function logActivity($user, $activity)
    {
        ActivityLog::create([
            'user_id' => $user->id,
            'activity' => $activity,
        ]);
    }
}
