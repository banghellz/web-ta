<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserDetail extends Model
{
    use HasFactory;

    protected $table = 'user_details';

    protected $fillable = [
        'user_id',
        'nama',
        'nim',
        'no_koin',
        'prodi',
        'pict',
        'rfid_uid',
        'koin'
    ];

    // Relasi dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi dengan RfidTag
    public function rfidTag()
    {
        return $this->belongsTo(RfidTag::class, 'rfid_uid', 'uid');
    }

    // Relasi dengan Item yang sedang dipinjam
    public function borrowedItems()
    {
        return $this->hasMany(Item::class, 'user_id', 'user_id');
    }

    /**
     * Mengurangi koin saat user meminjam item
     * 
     * @param int $itemId
     * @param int $coinCost (default 1)
     * @return bool
     */
    public function borrowItem($itemId, $coinCost = 1)
    {
        // Cek apakah user memiliki koin yang cukup
        if ($this->koin < $coinCost) {
            return false; // Koin tidak cukup
        }

        // Cek apakah item tersedia
        $item = Item::find($itemId);
        if (!$item || $item->available != 1 || $item->user_id != null) {
            return false; // Item tidak tersedia atau sudah dipinjam
        }

        try {
            DB::transaction(function () use ($item, $coinCost) {
                // Update item - set user_id dan ubah available
                $item->update([
                    'user_id' => $this->user_id,
                    'available' => 0
                ]);

                // Kurangi koin
                $this->decrement('koin', $coinCost);

                // Log aktivitas peminjaman
                LogPeminjaman::create([
                    'item_id' => $item->id,
                    'item_name' => $item->nama_barang,
                    'activity_type' => 'pinjam',
                    'timestamp' => now(),
                    'user_id' => $this->user_id,
                    'username' => $this->nama
                ]);
            });

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Mengembalikan koin saat user mengembalikan item
     * 
     * @param int $itemId
     * @param int $coinReturn (default 1)
     * @return bool
     */
    public function returnItem($itemId, $coinReturn = 1)
    {
        // Cek apakah item sedang dipinjam oleh user ini
        $item = Item::where('id', $itemId)
            ->where('user_id', $this->user_id)
            ->first();

        if (!$item) {
            return false; // Item tidak ditemukan atau bukan milik user ini
        }

        try {
            DB::transaction(function () use ($item, $coinReturn) {
                // Update item - hapus user_id dan ubah available
                $item->update([
                    'user_id' => null,
                    'available' => 1
                ]);

                // Kembalikan koin
                $this->increment('koin', $coinReturn);

                // Log aktivitas pengembalian
                LogPeminjaman::create([
                    'item_id' => $item->id,
                    'item_name' => $item->nama_barang,
                    'activity_type' => 'kembali',
                    'timestamp' => now(),
                    'user_id' => $this->user_id,
                    'username' => $this->nama
                ]);
            });

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Mengembalikan semua item yang sedang dipinjam oleh user
     * 
     * @return bool
     */
    public function returnAllItems()
    {
        $borrowedItems = Item::where('user_id', $this->user_id)->get();

        if ($borrowedItems->isEmpty()) {
            return true; // Tidak ada item yang dipinjam
        }

        try {
            DB::transaction(function () use ($borrowedItems) {
                foreach ($borrowedItems as $item) {
                    // Update item
                    $item->update([
                        'user_id' => null,
                        'available' => 1
                    ]);

                    // Kembalikan koin
                    $this->increment('koin', 1);

                    // Log aktivitas pengembalian
                    LogPeminjaman::create([
                        'item_id' => $item->id,
                        'item_name' => $item->nama_barang,
                        'activity_type' => 'kembali',
                        'timestamp' => now(),
                        'user_id' => $this->user_id,
                        'username' => $this->nama
                    ]);
                }
            });

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Mendapatkan jumlah item yang sedang dipinjam
     * 
     * @return int
     */
    public function getBorrowedItemsCount()
    {
        return Item::where('user_id', $this->user_id)->count();
    }

    /**
     * Mengecek apakah user memiliki koin yang cukup untuk meminjam
     * 
     * @param int $coinCost
     * @return bool
     */
    public function canBorrow($coinCost = 1)
    {
        return $this->koin >= $coinCost;
    }

    /**
     * Menambah koin (untuk admin/top up)
     * 
     * @param int $amount
     * @return bool
     */
    public function addCoins($amount)
    {
        try {
            $this->increment('koin', $amount);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Auto-sync koin berdasarkan item yang sedang dipinjam
     * Berguna untuk sinkronisasi jika ada ketidaksesuaian data
     * 
     * @return bool
     */
    public function syncCoins()
    {
        try {
            $borrowedCount = $this->getBorrowedItemsCount();
            $expectedCoins = 10 - $borrowedCount; // Asumsi koin awal 10

            $this->update(['koin' => max(0, $expectedCoins)]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Scope untuk mendapatkan user dengan koin tertentu
     */
    public function scopeWithCoins($query, $minCoins)
    {
        return $query->where('koin', '>=', $minCoins);
    }

    /**
     * Scope untuk mendapatkan user yang sedang meminjam item
     */
    public function scopeCurrentlyBorrowing($query)
    {
        return $query->whereHas('borrowedItems');
    }

    /**
     * Accessor untuk mendapatkan status peminjaman
     */
    public function getIsBorrowingAttribute()
    {
        return $this->getBorrowedItemsCount() > 0;
    }

    /**
     * Accessor untuk mendapatkan maksimal item yang bisa dipinjam
     */
    public function getMaxBorrowableItemsAttribute()
    {
        return $this->koin;
    }
}
