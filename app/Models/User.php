<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'roles',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
        'roles' => 'string',
    ];

    public function donasis()
    {
        return $this->hasMany(Donasi::class, 'user_id');
    }

    public function donaturDonasis()
    {
        return $this->hasMany(Donasi::class, 'donatur_id');
    }

    public function pengeluarans()
    {
        return $this->hasMany(Pengeluaran::class, 'pengurus');
    }

    public function isAdmin()
    {
        return $this->roles === 'admin';
    }

    public function isTakmir()
    {
        return $this->roles === 'takmir';
    }

    public function isWarga()
    {
        return $this->roles === 'warga';
    }
}