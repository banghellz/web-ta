<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class CompleteProfileController extends Controller
{
    // Method untuk menampilkan form
    public function index()
    {
        $user = Auth::user();
        // Ambil NIM dari session jika ada
        $extractedNim = session('extracted_nim');

        return view('user.complete-profile', compact('user', 'extractedNim'));
    }

    // Method untuk memproses form dan menyimpan data
    public function store(Request $request)
    {
        $user = Auth::user();

        // Validasi form
        $request->validate([
            'nim' => 'required|integer|min:0|unique:user_details,nim', // Tambahkan unique validation
            'no_koin' => 'required|numeric',
            'prodi' => 'required|string|max:50',
            'pict' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            // Upload gambar
            $file = $request->file('pict');
            $namaFileFoto = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('profile_pictures'), $namaFileFoto);

            // Simpan ke relasi user_detail
            $user->detail()->create([
                'nama' => $user->name,
                'nim' => $request->nim,
                'no_koin' => $request->no_koin,
                'prodi' => $request->prodi,
                'pict' => $namaFileFoto,
            ]);

            // Hapus data NIM dari session jika ada
            session()->forget('extracted_nim');

            // Redirect ke dashboard setelah berhasil
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard.index'),
                default => redirect()->route('user.dashboard'),
            };
        } catch (QueryException $e) {
            // Tangkap error database
            if ($e->errorInfo[1] == 1062) { // 1062 adalah error code untuk duplikasi
                return back()
                    ->withInput()
                    ->withErrors(['nim' => 'NIM sudah digunakan oleh pengguna lain.']);
            }

            // Error database lainnya
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        } catch (\Exception $e) {
            // Error umum lainnya
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
