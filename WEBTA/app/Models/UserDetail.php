<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetail extends Model
{
    protected $fillable = ['nama', 'nim', 'no_koin', 'prodi', 'pict'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
