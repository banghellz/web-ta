<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DahsboardController extends Controller
{
    public function index(Request $request)
    {
        // Cek apakah user sudah login
        if (!$request->user()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        // Ambil data user yang sedang login
        $user = $request->user();

        // Tampilkan dashboard untuk guest
        return view('guest.dashboard.index', compact('user'));
    }
}
