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
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
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
        return $this->hasMany(Pengeluaran::class, 'user_id');
    }

    public function isAdmin(): bool
    {
        return $this->roles === 'admin';
    }

    public function isTakmir(): bool
    {
        return $this->roles === 'takmir';
    }

    public function isWarga(): bool
    {
        return $this->roles === 'warga';
    }

    public function hasRole(string $role): bool
    {
        return $this->roles === $role;
    }
}