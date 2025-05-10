<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Donasi;
use Illuminate\Http\JsonResponse;

class DonasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $donasi = Donasi::with('user')->get();
        return response()->json($donasi);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'jumlah_donasi' => 'required|numeric|min:0',
            'date' => 'required|date',
            'note' => 'nullable|string',
            'user_id' => 'required|exists:users,id'
        ]);

        $donasi = Donasi::create($request->all());
        return response()->json($donasi, 201);
    }
}
