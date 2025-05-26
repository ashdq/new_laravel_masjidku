<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artikel extends Model
{
    protected $table = 'artikel';

    protected $fillable = [
        'judul',
        'gambar_artikel',
        'sumber',
        'isi_artikel',
        'tanggal_artikel'
    ];
    protected $casts = [
        'tanggal_artikel' => 'date'
    ];

}
