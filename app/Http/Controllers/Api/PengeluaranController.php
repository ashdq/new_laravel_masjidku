<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengeluaran;
use Illuminate\Http\JsonResponse;

class PengeluaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $pengeluaran = Pengeluaran::with('user')->get();
        return response()->json($pengeluaran);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'keperluan' => 'required|string|max:255',
            'jumlah_pengeluaran' => 'required|numeric|min:0',
            'tanggal' => 'required|date',
            'deskripsi' => 'nullable|string',
            'user_id' => 'required|exists:users,id'
        ]);

        $pengeluaran = Pengeluaran::create($request->all());
        return response()->json($pengeluaran, 201);
    }

    public function show($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        return response()->json($pengeluaran);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $request->validate([
            'keperluan' => 'sometimes|string|max:255',
            'jumlah_pengeluaran' => 'sometimes|numeric|min:0',
            'tanggal' => 'sometimes|date',
            'deskripsi' => 'nullable|string',
            'user_id' => 'sometimes|exists:users,id'
        ]);

        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->update($request->all());

        return response()->json($pengeluaran);
    }
}