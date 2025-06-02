<?php

// 2. SuperAdminMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $user = Auth::user();

        // SuperAdmin middleware hanya mengizinkan role: superadmin
        if ($user->role !== 'superadmin') {
            // Redirect berdasarkan role yang dimiliki
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard.index'),
                'user' => redirect()->route('user.dashboard.index'),
                'guest' => redirect()->route('guest.dashboard.index'),
                default => redirect('/')->with('error', 'Akses tidak diizinkan')
            };
        }

        return $next($request);
    }
}
