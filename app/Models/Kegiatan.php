<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $table = 'kegiatan';

    protected $fillable = [
        'nama_kegiatan',
        'tanggal_kegiatan',
        'waktu_kegiatan'
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
        'waktu_kegiatan' => 'datetime'
    ];
}