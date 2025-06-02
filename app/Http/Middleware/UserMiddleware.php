<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // PERBAIKAN: Cek apakah user sudah login terlebih dahulu
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $user = Auth::user();

        // PERBAIKAN: Cek apakah user memiliki role yang diizinkan
        // User middleware mengizinkan role: user, admin, superadmin
        if (!in_array($user->role, ['user', 'admin', 'superadmin'])) {
            // Redirect berdasarkan role yang dimiliki
            return match ($user->role) {
                'guest' => redirect()->route('guest.dashboard.index'),
                default => redirect('/')->with('error', 'Akses tidak diizinkan')
            };
        }

        return $next($request);
    }
}
