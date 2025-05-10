<?php

namespace App\Http\Controllers;

use App\Models\Sholat;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SholatController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $shalat = Sholat::all();
        return response()->json([
            'status' => 'success',
            'data' => $shalat
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'imsak' => 'required',
            'subuh' => 'required',
            'terbit' => 'required',
            'dhuha' => 'required',
            'dzuhur' => 'required',
            'ashar' => 'required',
            'maghrib' => 'required',
            'isya' => 'required'
        ]);

        // $shalat = Sholat::create([
        //     'tanggal' => $request->tanggal,
        //     'imsak' => $request->imsak,
        //     'subuh' => $request->subuh,
        //     'terbit' => $request->terbit,
        //     'dhuha' => $request->dhuha,
        //     'dzuhur' => $request->dzuhur,
        //     'ashar' => $request->ashar,
        //     'maghrib' => $request->maghrib,
        //     'isya' => $request->isya
        // ]);
        $sholat = Sholat::create($request->all());
        return response()->json($sholat, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $shalat = Sholat::find($id);
        
        if (!$shalat) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jadwal sholat tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $shalat
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $shalat = Sholat::find($id);

        if (!$shalat) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jadwal sholat tidak ditemukan'
            ], 404);
        }

        $request->validate([
            'tanggal' => 'required|date',
            'imsak' => 'required|date_format:H:i',
            'subuh' => 'required|date_format:H:i',
            'terbit' => 'required|date_format:H:i',
            'dhuha' => 'required|date_format:H:i',
            'dzuhur' => 'required|date_format:H:i',
            'ashar' => 'required|date_format:H:i',
            'maghrib' => 'required|date_format:H:i',
            'isya' => 'required|date_format:H:i'
        ]);

        $shalat->update([
            'tanggal' => $request->tanggal,
            'imsak' => $request->imsak,
            'subuh' => $request->subuh,
            'terbit' => $request->terbit,
            'dhuha' => $request->dhuha,
            'dzuhur' => $request->dzuhur,
            'ashar' => $request->ashar,
            'maghrib' => $request->maghrib,
            'isya' => $request->isya
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal sholat berhasil diperbarui',
            'data' => $shalat
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $shalat = Sholat::find($id);

        if (!$shalat) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jadwal sholat tidak ditemukan'
            ], 404);
        }

        $shalat->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Jadwal sholat berhasil dihapus'
        ]);
    }
}
