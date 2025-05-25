<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aspirasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

    
class AspirasiController extends Controller
{
    // warga kirim aspirasi
    public function store(Request $request)
    {
        $request->validate([
            'jenis_aspirasi' => 'required|string|in:fasilitas masjid,kegiatan dan program,pengelolaan masjid,social dan lingkungan,dakwah dan Pendidikan',
            'description' => 'required|string',
        ]);

        $user = Auth::user();

        if ($user->roles !== 'warga') {
            return response()->json(['message' => 'Hanya warga yang dapat mengirim aspirasi.'], 403);
        }

        $aspirasi = Aspirasi::create([
            'nama' => $user->name,
            'jenis_aspirasi' => $request->jenis_aspirasi,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Aspirasi berhasil dikirim.',
            'data' => $aspirasi
        ], 201);
    }

    // admin dan takmir lihat semua aspirasi
    public function index()
    {
        $user = Auth::user();

        if (!in_array($user->roles, ['admin', 'takmir'])) {
            return response()->json(['message' => 'Hanya admin atau takmir yang dapat melihat aspirasi.'], 403);
        }

        $aspirasis = Aspirasi::orderBy('created_at', 'desc')->get();

        return response()->json([
            'message' => 'Data aspirasi berhasil diambil.',
            'data' => $aspirasis
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();

        if (!in_array($user->roles, ['admin', 'takmir'])) {
            return response()->json(['message' => 'Hanya admin atau takmir yang dapat menghapus aspirasi.'], 403);
        }

        $aspirasi = Aspirasi::find($id);

        if (!$aspirasi) {
            return response()->json(['message' => 'Aspirasi tidak ditemukan.'], 404);
        }

        $aspirasi->delete();

        return response()->json(['message' => 'Aspirasi berhasil dihapus.'], 200);
    }

}

