<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Domain yang diizinkan untuk role 'user'
    private $allowedDomains = ['atmi.ac.id', 'student.atmi.ac.id'];

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect($_ENV['GOOGLE_REDIRECT_URI']);
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $client = new \Google_Client(['client_id' => $_ENV['GOOGLE_CLIENT_ID']]);
            $client->addScope("email");
            $payload = $client->verifyIdToken($request->credential);

            if ($payload) {
                $googleUser = $payload;
                $email = strtolower($googleUser['email']);

                // Check email domain
                $domain = $this->extractDomainFromEmail($email);
                $isAllowedDomain = in_array($domain, $this->allowedDomains);

                $user = User::firstOrNew(['email' => $email]);
                $isNewUser = !$user->exists;

                if ($isNewUser) {
                    $user->uuid = Str::uuid();
                    // Set role based on domain
                    $user->role = $isAllowedDomain ? 'user' : 'guest';
                }

                $user->name = $googleUser['name'];
                $user->password = bcrypt(Str::random(16));

                // Store Google photo URL if available
                if (isset($googleUser['picture'])) {
                    $user->google_photo_url = $googleUser['picture'];
                }

                // Extract NIM from email (only for allowed domains)
                $nim = $isAllowedDomain ? $this->extractNimFromEmail($email) : null;

                $user->save();

                Auth::guard("web")->login($user, true);

                // Log the activity
                $this->logActivity($user, $isNewUser ? 'register' : 'login');

                // Redirect logic based on role
                if ($user->role === 'guest') {
                    // Guest goes directly to dashboard without complete profile
                    return redirect()->route('guest.dashboard.index');
                }

                // For users with allowed domains
                if ($user->role === 'user' && !$user->detail) {
                    // If NIM was successfully extracted, add to session for use in form
                    if ($nim) {
                        session(['extracted_nim' => $nim]);
                    }

                    // Store Google photo URL in session for complete profile form
                    if (isset($googleUser['picture'])) {
                        session(['google_photo_url' => $googleUser['picture']]);
                    }

                    return redirect()->route('user.complete-profile');
                }

                // Redirect based on role
                return match ($user->role) {
                    'superadmin' => redirect()->route('superadmin.dashboard.index'),
                    'admin' => redirect()->route('admin.dashboard.index'),
                    'user' => redirect()->route('user.dashboard.index'),
                    'guest' => redirect()->route('guest.dashboard.index'),
                    default => redirect()->route('user.dashboard.index'),
                };
            } else {
                throw new \Exception('Invalid Google ID token');
            }
        } catch (\Exception $e) {
            Log::error('Google authentication failed: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Login gagal: ' . $e->getMessage());
        }
    }

    /**
     * Extract domain from email
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
     * Extract NIM from student email
     * Email format: nama.nim@domain.com
     * 
     * @param string $email
     * @return string|null
     */
    private function extractNimFromEmail($email)
    {
        // Pattern to find NIM in email name part
        // Assuming email format is nama.20222007@domain.com
        if (preg_match('/\.(\d{8})@/', $email, $matches)) {
            return $matches[1]; // Get the matching group (8 digit number)
        }
        return null; // If not found
    }

    public function logout(Request $request)
    {
        // Log the logout activity before logging out
        if (Auth::check()) {
            $this->logActivity(Auth::user(), 'logout');
        }

        Auth::logout();

        // Clear any session data
        session()->forget(['extracted_nim', 'google_photo_url']);

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
