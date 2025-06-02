<?php

// 1. AdminMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $user = Auth::user();

        // Admin middleware mengizinkan role: admin, superadmin
        if (!in_array($user->role, ['admin', 'superadmin'])) {
            // Redirect berdasarkan role yang dimiliki
            return match ($user->role) {
                'user' => redirect()->route('user.dashboard.index'),
                'guest' => redirect()->route('guest.dashboard.index'),
                default => redirect('/')->with('error', 'Akses tidak diizinkan')
            };
        }

        return $next($request);
    }
}
