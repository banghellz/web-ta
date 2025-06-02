<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LogPeminjaman extends Model
{
    use HasFactory;

    protected $table = 'log_peminjaman';

    // Since your table doesn't have timestamps columns, disable them
    public $timestamps = false;

    protected $fillable = [
        'item_id',
        'item_name',
        'activity_type',
        'timestamp',
        'user_id',
        'username'
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'activity_type' => 'string',
        'item_id' => 'integer',
        'user_id' => 'integer'
    ];

    /**
     * Relationship with User
     * 
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
