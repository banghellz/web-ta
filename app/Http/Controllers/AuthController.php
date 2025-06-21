<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Notification; // Add this import
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Domain yang diizinkan untuk role 'user'
    private $allowedDomains = ['atmi.ac.id', 'student.atmi.ac.id'];

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect($_ENV['GOOGLE_REDIRECT_URI']);
    }

    public function _handleGoogleCallback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->redirect($_ENV['GOOGLE_REDIRECT_URI'])->stateless()->user();;
            $email = strtolower($googleUser->email);

            // Cek domain email
            $domain = $this->extractDomainFromEmail($email);
            $isAllowedDomain = in_array($domain, $this->allowedDomains);

            $user = User::firstOrNew(['email' => $email]);
            $isNewUser = !$user->exists;

            if ($isNewUser) {
                $user->uuid = Str::uuid();
                // Set role berdasarkan domain
                $user->role = $isAllowedDomain ? 'user' : 'guest';
            }

            $user->name = $googleUser->name;
            $user->password = bcrypt(Str::random(16));

            // Ekstrak NIM dari email (hanya untuk domain yang diizinkan)
            $nim = $isAllowedDomain ? $this->extractNimFromEmail($email) : null;

            $user->save();

            Auth::login($user);

            // Log the activity
            $this->logActivity($user, $isNewUser ? 'register' : 'login');





            // Redirect logic berdasarkan role
            if ($user->role === 'guest') {
                // Guest langsung ke dashboard tanpa complete profile
                return redirect()->route('guest.dashboard.index');
            }

            // Untuk user dengan domain yang diizinkan
            if ($user->role === 'user' && !$user->detail) {
                // Jika NIM berhasil diekstrak, tambahkan ke session untuk digunakan di form
                if ($nim) {
                    session(['extracted_nim' => $nim]);
                }
                return redirect()->route('user.complete-profile');
            }

            // Redirect berdasarkan role dengan nama route
            return match ($user->role) {
                'superadmin' => redirect()->route('superadmin.dashboard.index'),
                'admin' => redirect()->route('admin.dashboard.index'),
                'user' => redirect()->route('user.dashboard.index'),
                'guest' => redirect()->route('guest.dashboard.index'),
                default => redirect()->route('user.dashboard.index'),
            };
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            return redirect('/login')->with('error', 'Session expired, please try again');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Login gagal: ' . $e->getMessage());
        }
    }
    public function handleGoogleCallback(Request $request)
    {
        // return response()->json(['message' => $request->all()]);
        // Panggil metode _handleGoogleCallback untuk menangani callback
        // response()->json(['message' => 'Handling Google Callback']);
        // sleep(2);
        try {
            $client = new \Google_Client(['client_id' => $_ENV['GOOGLE_CLIENT_ID']]); // Specify the client ID
            $client->addScope("email");
            $payload = $client->verifyIdToken($request->credential);
            // return response()->json(['payload' => $payload]);
            // var_dump($payload);
            if ($payload) {
                $googleUser = $payload;
                $email = strtolower($googleUser['email']);

                // Cek domain email
                $domain = $this->extractDomainFromEmail($email);
                $isAllowedDomain = in_array($domain, $this->allowedDomains);

                $user = User::firstOrNew(['email' => $email]);
                $isNewUser = !$user->exists;
                // var_dump($user);
                if ($isNewUser) {
                    $user->uuid = Str::uuid();
                    // Set role berdasarkan domain
                    $user->role = $isAllowedDomain ? 'user' : 'guest';
                }

                $user->name = $googleUser['name'];
                $user->password = bcrypt(Str::random(16));

                // Ekstrak NIM dari email (hanya untuk domain yang diizinkan)
                $nim = $isAllowedDomain ? $this->extractNimFromEmail($email) : null;

                $user->save();

                Auth::guard("web")->login($user,  true);
                // var_dump(Auth::user());
                // Log the activity
                $this->logActivity($user, $isNewUser ? 'register' : 'login');




                // return response()->json(['message' => 'Login berhasil', 'user' => $user]);
                // return redirect()->route('superadmin.dashboard.index');
                // Redirect logic berdasarkan role
                if ($user->role === 'guest') {
                    // Guest langsung ke dashboard tanpa complete profile
                    return redirect()->route('guest.dashboard.index');
                }

                // Untuk user dengan domain yang diizinkan
                if ($user->role === 'user' && !$user->detail) {
                    // Jika NIM berhasil diekstrak, tambahkan ke session untuk digunakan di form
                    if ($nim) {
                        session(['extracted_nim' => $nim]);
                    }
                    return redirect()->route('user.complete-profile');
                }

                // Redirect berdasarkan role dengan nama route
                return match ($user->role) {
                    'superadmin' => redirect()->route('superadmin.dashboard.index'),
                    'admin' => redirect()->route('admin.dashboard.index'),
                    'user' => redirect()->route('user.dashboard.index'),
                    'guest' => redirect()->route('guest.dashboard.index'),
                    default => redirect()->route('user.dashboard.index'),
                };
            } else {
                // Invalid ID token
                // return response()->json(['payload' => $payload]);
            }
        } catch (\Exception $e) {
            // return response()->json(['error' => 'Login gagal: ' . $e->getMessage()], 500);
            return redirect('/login')->with('error', 'Login gagal: ' . $e->getMessage());
        }
    }

    /**
     * Ekstrak domain dari email
     * 
     * @param string $email
     * @return string|null
     */
    private function extractDomainFromEmail($email)
    {
        $parts = explode('@', $email);
        return count($parts) === 2 ? $parts[1] : null;
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
