<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sholat extends Model
{
    protected $table = "sholat";
    protected $fillable = [
        'tanggal',
        'imsak',
        'subuh',
        'terbit',
        'dhuha',
        'dzuhur',
        'ashar',
        'maghrib',
        'isya'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'imsak' => 'datetime',
        'subuh' => 'datetime',
        'terbit' => 'datetime',
        'dhuha' => 'datetime',
        'dzuhur' => 'datetime',
        'ashar' => 'datetime',
        'maghrib' => 'datetime',
        'isya' => 'datetime'
    ];
}
