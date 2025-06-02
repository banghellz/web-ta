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
        'user_id' // Tambahan untuk tracking siapa yang meminjam
    ];

    protected $casts = [
        'available' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relasi dengan User yang meminjam
    public function borrower()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi dengan UserDetail yang meminjam
    public function borrowerDetail()
    {
        return $this->belongsTo(UserDetail::class, 'user_id', 'user_id');
    }

    // Scope untuk item yang tersedia
    public function scopeAvailable($query)
    {
        return $query->where('available', '>', 0)->whereNull('user_id');
    }

    // Scope untuk item yang habis
    public function scopeOutOfStock($query)
    {
        return $query->where('available', '<=', 0);
    }

    // Scope untuk item yang sedang dipinjam
    public function scopeBorrowed($query)
    {
        return $query->whereNotNull('user_id');
    }

    // Scope untuk item yang tidak dipinjam
    public function scopeNotBorrowed($query)
    {
        return $query->whereNull('user_id');
    }

    // Accessor untuk status ketersediaan
    public function getStatusAttribute()
    {
        if ($this->user_id) {
            return 'Borrowed';
        }
        return $this->available > 0 ? 'Available' : 'Out of Stock';
    }

    // Accessor untuk status badge class
    public function getStatusBadgeClassAttribute()
    {
        if ($this->user_id) {
            return 'bg-warning';
        }
        return $this->available > 0 ? 'bg-success' : 'bg-danger';
    }

    // Boot model events
    protected static function boot()
    {
        parent::boot();

        // Auto sync koin ketika item dipinjam/dikembalikan
        static::updated(function ($item) {
            if ($item->isDirty('user_id')) {
                // Jika user_id berubah, sync koin untuk user lama dan baru
                $originalUserId = $item->getOriginal('user_id');
                $newUserId = $item->user_id;

                // Sync koin untuk user lama (yang mengembalikan item)
                if ($originalUserId) {
                    $oldUserDetail = UserDetail::where('user_id', $originalUserId)->first();
                    if ($oldUserDetail) {
                        $oldUserDetail->syncKoin();
                    }
                }

                // Sync koin untuk user baru (yang meminjam item)
                if ($newUserId) {
                    $newUserDetail = UserDetail::where('user_id', $newUserId)->first();
                    if ($newUserDetail) {
                        $newUserDetail->syncKoin();
                    }
                }
            }
        });
    }
}
