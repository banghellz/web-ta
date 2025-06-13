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
            'pict' => 'required|image|mimes:jpg,jpeg,png|max:10480',
        ]);

        try {
            // Upload gambar
            $file = $request->file('pict');
            $namaFileFoto = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('profile_pictures'), $namaFileFoto);

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
                'rfid_uid' => $rfidUid, // Simpan RFID UID atau null jika tidak ada yang tersedia
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

                // Log sukses pengiriman email
                Log::info('Registration success email sent to: ' . $user->email);

                // Set flash message untuk memberitahu user bahwa email telah dikirim
                session()->flash('email_sent', 'A confirmation email has been sent to your email address.');
            } catch (\Exception $emailException) {
                // Jika pengiriman email gagal, log error tapi tetap lanjutkan proses
                Log::error('Failed to send registration email to: ' . $user->email . '. Error: ' . $emailException->getMessage());

                // Set flash message untuk memberitahu user bahwa email gagal dikirim
                session()->flash('email_failed', 'Registration successful, but we couldn\'t send the confirmation email. Please contact support if needed.');
            }

            // Redirect ke dashboard setelah berhasil
            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard.index'),
                default => redirect()->route('user.dashboard.index'),
            };
        } catch (QueryException $e) {
            DB::rollBack();

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
            DB::rollBack();

            // Error umum lainnya
            return back()
                ->withInput()
                ->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
