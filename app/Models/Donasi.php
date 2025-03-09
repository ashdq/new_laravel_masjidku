<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donasi extends Model
{
    protected $fillable = [
        'nama',
        'jumlah_donasi',
        'date',
        'note',
        'user_id'
    ];

    protected $casts = [
        'date' => 'date',
        'jumlah_donasi' => 'decimal:2'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
