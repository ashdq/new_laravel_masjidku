<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KeuanganMasjid;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function index()
    {
        $keuangan = KeuanganMasjid::first();
        if (!$keuangan) {
            $keuangan = KeuanganMasjid::create(['saldo' => 0]);
        }
        
        return response()->json(['data' => $keuangan]);
    }

    public function saldo()
    {
        $keuangan = KeuanganMasjid::first();
        $saldo = $keuangan ? $keuangan->saldo : 0;
        
        return response()->json(['saldo' => $saldo]);
    }
}
