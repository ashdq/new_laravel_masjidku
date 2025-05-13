<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeuanganMasjid extends Model
{
    use HasFactory;
    protected $table = 'keuangan_masjid';
    protected $fillable = ['saldo', 'last_updated'];
    protected $casts = [
        'saldo' => 'decimal:2',
        'last_updated' => 'datetime'
    ];

    // Method untuk update saldo
    // Method to update the balance
    public static function updateSaldo($amount, $operation = 'add')
    {
        $keuangan = self::first();
        
        if (!$keuangan) {
            $keuangan = self::create(['saldo' => 0]);
        }

        if ($operation === 'add') {
            $keuangan->saldo += $amount;
        } else {
            $keuangan->saldo -= $amount;
        }

        $keuangan->last_updated = now();
        $keuangan->save();

        return $keuangan;
    }
}
