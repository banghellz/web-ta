<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    protected $attributes = [
        'koin' => 10, // Default koin adalah 10
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
     * Sinkronisasi koin berdasarkan jumlah item yang dipinjam
     * Koin = 10 - jumlah item yang dipinjam
     */
    public function syncKoin()
    {
        $borrowedItemsCount = $this->borrowedItems()->count();
        $newKoinValue = max(0, 10 - $borrowedItemsCount); // Minimal 0, maksimal 10

        $this->update(['koin' => $newKoinValue]);

        return $this;
    }

    /**
     * Get jumlah item yang sedang dipinjam
     */
    public function getBorrowedItemsCountAttribute()
    {
        return $this->borrowedItems()->count();
    }

    /**
     * Get koin yang tersedia untuk meminjam item baru
     */
    public function getAvailableKoinAttribute()
    {
        return $this->koin;
    }

    /**
     * Cek apakah user bisa meminjam item (masih ada koin)
     */
    public function canBorrowItem()
    {
        return $this->koin > 0;
    }

    /**
     * Kurangi koin ketika meminjam item
     */
    public function borrowItem($itemId)
    {
        if (!$this->canBorrowItem()) {
            return false; // Tidak bisa meminjam karena koin habis
        }

        // Update item dengan user_id
        $item = Item::find($itemId);
        if ($item && is_null($item->user_id)) {
            $item->update(['user_id' => $this->user_id]);

            // Sinkronisasi koin
            $this->syncKoin();

            return true;
        }

        return false;
    }

    /**
     * Kembalikan item dan tambah koin
     */
    public function returnItem($itemId)
    {
        $item = Item::where('id', $itemId)
            ->where('user_id', $this->user_id)
            ->first();

        if ($item) {
            // Set user_id menjadi null (item dikembalikan)
            $item->update(['user_id' => null]);

            // Sinkronisasi koin
            $this->syncKoin();

            return true;
        }

        return false;
    }

    /**
     * Kembalikan semua item yang dipinjam user
     */
    public function returnAllItems()
    {
        $this->borrowedItems()->update(['user_id' => null]);
        $this->syncKoin();

        return $this;
    }

    /**
     * Boot model events
     */
    protected static function boot()
    {
        parent::boot();

        // Auto sync koin setelah model dibuat
        static::created(function ($userDetail) {
            $userDetail->syncKoin();
        });
    }
}
