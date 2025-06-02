<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RfidTag extends Model
{
    use HasFactory;

    protected $table = 'rfid_tags';

    protected $fillable = [
        'uid',
        'status',
        'notes'
    ];

    // Relasi dengan UserDetail
    public function userDetail()
    {
        return $this->belongsTo(UserDetail::class, 'uid', 'rfid_uid');
    }

    // Mendapatkan satu tag RFID yang tersedia
    public static function getAvailableTag()
    {
        return self::where('status', 'Available')->first();
    }

    // Mengubah status tag menjadi Used
    public function markAsUsed()
    {
        $this->status = 'Used';
        $this->save();

        return $this;
    }

    // Mengubah status tag menjadi Available
    public function markAsAvailable()
    {
        $this->status = 'Available';
        $this->save();

        return $this;
    }
}
