<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissingTools extends Model
{
    use HasFactory;

    protected $table = 'missing_tools';

    protected $fillable = [
        'item_id',
        'epc',
        'nama_barang',
        'user_id',
        'status',
        'reported_at',
        'reclaimed_at'
    ];

    protected $casts = [
        'reported_at' => 'datetime',
        'reclaimed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relasi dengan Item
    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    // Relasi dengan User yang bertanggung jawab
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi dengan UserDetail
    public function userDetail()
    {
        return $this->belongsTo(UserDetail::class, 'user_id', 'user_id');
    }

    // Scope untuk status pending
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope untuk status completed
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Scope untuk status cancelled
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Scope untuk data terbaru
    public function scopeLatest($query)
    {
        return $query->orderBy('reported_at', 'desc');
    }

    // Accessor untuk status badge class
    public function getStatusBadgeClassAttribute()
    {
        return match ($this->status) {
            'pending' => 'bg-danger',
            'completed' => 'bg-success',
            'cancelled' => 'bg-warning',
            default => 'bg-secondary'
        };
    }

    // Accessor untuk status text
    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'completed' => 'Reclaimed',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    // Accessor untuk durasi missing (dalam hari)
    public function getDurationAttribute()
    {
        $endDate = $this->reclaimed_at ?? now();
        return $this->reported_at->diffInDays($endDate);
    }

    // Accessor untuk format durasi yang readable
    public function getDurationTextAttribute()
    {
        $duration = $this->duration;

        if ($duration == 0) {
            return 'Hari ini';
        } elseif ($duration == 1) {
            return '1 hari';
        } else {
            return $duration . ' hari';
        }
    }

    // Accessor untuk action date text
    public function getActionDateTextAttribute()
    {
        if ($this->status === 'completed') {
            return 'Reclaimed Date';
        } elseif ($this->status === 'cancelled') {
            return 'Cancelled Date';
        }
        return 'Action Date';
    }

    // Check if missing tool can be cancelled
    public function canBeCancelled()
    {
        return $this->status === 'pending';
    }

    // Check if missing tool can be reclaimed
    public function canBeReclaimed()
    {
        return $this->status === 'pending';
    }
}
