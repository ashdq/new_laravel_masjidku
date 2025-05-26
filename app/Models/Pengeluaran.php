<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class Pengeluaran extends Model
{
    protected $table = "pengeluaran";
    protected $fillable = [
        'keperluan',
        'jumlah_pengeluaran',
        'tanggal',
        'deskripsi',
        'user_id',
        'nota'
    ];

    protected $casts = [
        'tanggal' => 'datetime',
        'jumlah_pengeluaran' => 'decimal:2'
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
