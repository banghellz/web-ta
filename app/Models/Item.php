<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'epc',
        'nama_barang',
        'available',
        'user_id' // Field untuk tracking siapa yang meminjam
    ];

    /**
     * Relasi dengan User yang sedang meminjam
     */
    public function borrower()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi dengan UserDetail yang sedang meminjam
     */
    public function borrowerDetail()
    {
        return $this->hasOneThrough(
            UserDetail::class,
            User::class,
            'id', // Foreign key on users table
            'user_id', // Foreign key on user_details table
            'user_id', // Local key on items table
            'id' // Local key on users table
        );
    }

    /**
     * Scope untuk item yang tersedia (sesuai dengan DashboardController)
     * Item available jika: available > 0 DAN tidak sedang dipinjam (user_id null)
     */
    public function scopeAvailable($query)
    {
        return $query->where('available', '>', 0)->whereNull('user_id');
    }

    /**
     * Scope untuk item yang sedang dipinjam
     */
    public function scopeBorrowed($query)
    {
        return $query->whereNotNull('user_id');
    }

    /**
     * Scope untuk item yang out of stock (sesuai dengan DashboardController)
     * Item out of stock jika: available = 0 ATAU available null
     */
    public function scopeOutOfStock($query)
    {
        return $query->where(function ($q) {
            $q->where('available', '<=', 0)
                ->orWhereNull('available');
        });
    }

    /**
     * Scope untuk item yang low stock (opsional, untuk pengembangan)
     */
    public function scopeLowStock($query, $threshold = 5)
    {
        return $query->where('available', '<=', $threshold)
            ->where('available', '>', 0);
    }

    /**
     * Accessor untuk mengecek apakah item sedang dipinjam
     */
    public function getIsBorrowedAttribute()
    {
        return $this->user_id !== null;
    }

    /**
     * Accessor untuk mengecek apakah item tersedia
     */
    public function getIsAvailableAttribute()
    {
        return $this->available > 0 && $this->user_id === null;
    }

    /**
     * Accessor untuk mendapatkan nama peminjam
     */
    public function getBorrowerNameAttribute()
    {
        return $this->borrowerDetail ? $this->borrowerDetail->nama : null;
    }

    /**
     * Accessor untuk mendapatkan status item dalam bentuk string
     */
    public function getStatusAttribute()
    {
        if ($this->user_id !== null) {
            return 'Dipinjam';
        } elseif ($this->available <= 0) {
            return 'Habis';
        } elseif ($this->available <= 5) {
            return 'Stok Rendah';
        } else {
            return 'Tersedia';
        }
    }

    /**
     * Accessor untuk mendapatkan status class untuk styling
     */
    public function getStatusClassAttribute()
    {
        if ($this->user_id !== null) {
            return 'warning'; // Item dipinjam
        } elseif ($this->available <= 0) {
            return 'danger'; // Item habis
        } elseif ($this->available <= 5) {
            return 'warning'; // Stok rendah
        } else {
            return 'success'; // Item tersedia
        }
    }

    /**
     * Method untuk meminjam item
     */
    public function borrowItem($userId)
    {
        if ($this->is_available) {
            $this->user_id = $userId;
            $this->available = $this->available - 1;
            return $this->save();
        }
        return false;
    }

    /**
     * Method untuk mengembalikan item
     */
    public function returnItem()
    {
        if ($this->is_borrowed) {
            $this->user_id = null;
            $this->available = $this->available + 1;
            return $this->save();
        }
        return false;
    }
}
