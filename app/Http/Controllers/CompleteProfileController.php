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
            'nim' => 'required|integer|min:0|unique:user_details,nim',
            'no_koin' => 'required|numeric',
            'prodi' => 'required|string|max:50',
            'pict' => 'required|image|mimes:jpg,jpeg,png|max:10480',
        ]);

        try {
            // Tentukan direktori upload
            $uploadPath = public_path('profile_pictures');

            // Pastikan direktori ada dan dapat ditulis
            if (!File::exists($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true);
            }

            // Cek apakah direktori dapat ditulis
            if (!is_writable($uploadPath)) {
                throw new \Exception('Direktori upload tidak dapat ditulis. Silakan hubungi administrator.');
            }

            // Upload gambar
            $file = $request->file('pict');
            $namaFileFoto = uniqid() . '.' . $file->getClientOriginalExtension();

            // Pindahkan file
            if (!$file->move($uploadPath, $namaFileFoto)) {
                throw new \Exception('Gagal mengupload file gambar.');
            }

            DB::beginTransaction();

            // Mencari RFID tag yang tersedia
            $rfidTag = RfidTag::getAvailableTag();
            $rfidUid = null;

            if ($rfidTag) {
                // Jika ada RFID yang tersedia, ambil UID-nya dan update statusnya
                $rfidUid = $rfidTag->uid;
                $rfidTag->markAsUsed();
            }

            // Simpan ke relasi user_detail
            $user->detail()->create([
                'nama' => $user->name,
                'nim' => $request->nim,
                'no_koin' => $request->no_koin,
                'prodi' => $request->prodi,
                'pict' => $namaFileFoto,
                'rfid_uid' => $rfidUid,
            ]);

            DB::commit();

            // Hapus data NIM dari session jika ada
            session()->forget('extracted_nim');

            // Kirim email notifikasi setelah berhasil mendaftar
            try {
                // Refresh user untuk memastikan relasi detail ter-load
                $user->refresh();
                $user->load('detail');
                Notification::userRegistered($user);
                Mail::to($user->email)->send(new AccountRegistrationSuccess($user));

                Log::info('Registration success email sent to: ' . $user->email);
                session()->flash('email_sent', 'A confirmation email has been sent to your email address.');
            } catch (\Exception $emailException) {
                Log::error('Failed to send registration email to: ' . $user->email . '. Error: ' . $emailException->getMessage());
                session()->flash('email_failed', 'Registration successful, but we couldn\'t send the confirmation email. Please contact support if needed.');
            }

            // Redirect ke dashboard setelah berhasil
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard.index'),
                default => redirect()->route('user.dashboard.index'),
            };
        } catch (QueryException $e) {
            DB::rollBack();

            // Hapus file yang sudah diupload jika ada error
            if (isset($namaFileFoto) && File::exists($uploadPath . '/' . $namaFileFoto)) {
                File::delete($uploadPath . '/' . $namaFileFoto);
            }

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

            // Hapus file yang sudah diupload jika ada error
            if (isset($namaFileFoto) && File::exists($uploadPath . '/' . $namaFileFoto)) {
                File::delete($uploadPath . '/' . $namaFileFoto);
            }

            return back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }
}
