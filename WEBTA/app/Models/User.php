<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'id'; // Primary key tetap id (auto-increment)
    public $incrementing = true;  // ID tetap auto-increment
    protected $keyType = 'int';   // Tipe ID adalah integer

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'uuid',
        'usn',
        'name',
        'email',
        'password',
        'last_login_at',
        'role', // Menambahkan role agar bisa diisi saat registrasi
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    /**
     * Boot function to generate UUID automatically.
     */
    protected static function boot()
    {
        parent::boot();

        // Hanya generate UUID jika user baru
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });

        // Normalisasi email ke lowercase
        static::saving(function ($model) {
            $model->email = strtolower($model->email);
        });
    }

    // Mutator untuk memastikan email lowercase
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }

    public function detail()
    {
        return $this->hasOne(UserDetail::class);
    }
}
