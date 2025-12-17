<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'otp_code',       // [BARU] Untuk menyimpan kode OTP
        'otp_expires_at', // [BARU] Untuk menyimpan waktu kadaluarsa OTP
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code', // Opsional: Sembunyikan OTP agar tidak muncul jika return user API
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'otp_expires_at' => 'datetime', // [PENTING] Agar dibaca sebagai objek waktu (Carbon)
    ];

    /**
     * Relasi: Satu User (Pembeli) bisa memiliki banyak Order.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}