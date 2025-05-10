<?php

namespace App\Http\Controllers;

use App\Models\Kegiatan;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function index()
    {
        $jadwal_kegiatan = Kegiatan::select('id','nama_kegiatan', 'waktu_kegiatan')->get();
        return response()->json(['data' => $jadwal_kegiatan]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string',
            'tanggal_kegiatan' => 'required|date',
            'waktu_kegiatan' => 'required'
        ]);

        $jadwal_kegiatan = Kegiatan::create($request->all());
        return response()->json($jadwal_kegiatan, 201);
    }

    public function show($id)
    {
        $jadwal_kegiatan = Kegiatan::findOrFail($id);
        return response()->json($jadwal_kegiatan);
    }

    public function update(Request $request, $id)
    {
        $jadwal_kegiatan = Kegiatan::findOrFail($id);
        
        $request->validate([
            'nama_kegiatan' => 'required|string',
            'tanggal_kegiatan' => 'required|date',
            'waktu_kegiatan' => 'required'
        ]);

        $jadwal_kegiatan->update($request->all());
        return response()->json($jadwal_kegiatan);
    }

    public function destroy($id)
    {
        $jadwal_kegiatan = Kegiatan::findOrFail($id);
        $jadwal_kegiatan->delete();
        return response()->json(null, 204);
    }
}
