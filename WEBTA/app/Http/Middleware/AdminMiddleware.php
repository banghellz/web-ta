<?php
// app/Http/Middleware/AdminMiddleware.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Periksa jika user tidak login
        if (!Auth::check()) {
            return redirect('login');
        }

        // Periksa role user
        $user = Auth::user();
        if ($user->role == 'admin' || $user->role == 'super_admin') {
            return $next($request);
        }

        // Jika bukan admin, alihkan ke halaman yang sesuai
        return redirect('/')->with('error', "Anda tidak memiliki akses ke halaman tersebut.");
    }
}
