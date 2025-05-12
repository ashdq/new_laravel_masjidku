<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donasi extends Model
{
    protected $table = 'donasi';
    protected $fillable = [
        'nama',
        'jumlah_donasi',
        'date',
        'note',
        'user_id',
        'donatur_id'
    ];

    protected $casts = [
        'date' => 'datetime',
        'jumlah_donasi' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    protected $with = ['user', 'donatur'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function donatur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'donatur_id');
    }

    public function scopeByDonatur($query, $donaturId)
    {
        return $query->where('donatur_id', $donaturId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}
