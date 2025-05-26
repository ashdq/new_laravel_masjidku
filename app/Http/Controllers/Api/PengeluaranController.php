<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengeluaran;
use App\Models\KeuanganMasjid;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PengeluaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $pengeluaran = Pengeluaran::with('user')->get();
        return response()->json(['data' => $pengeluaran]);
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
            'deskripsi' => 'required|string',
            'nota' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Check saldo cukup
        $keuangan = KeuanganMasjid::first();
        if (!$keuangan || $keuangan->saldo < $request->jumlah_pengeluaran) {
            return response()->json([
                'error' => 'Saldo tidak mencukupi. Saldo tersedia: Rp ' . number_format($keuangan->saldo ?? 0, 0, ',', '.')
            ], 400);
        }

        $data = [
            'keperluan' => $request->keperluan,
            'jumlah_pengeluaran' => $request->jumlah_pengeluaran,
            'tanggal' => $request->tanggal,
            'deskripsi' => $request->deskripsi,
            'user_id' => Auth::id(),
        ];

        if ($request->hasFile('nota')) {
            $file = $request->file('nota');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/nota', $filename);
            $data['nota'] = 'storage/nota/' . $filename;
        }

        $pengeluaran = Pengeluaran::create($data);

        // Update keuangan masjid
        KeuanganMasjid::updateSaldo($request->jumlah_pengeluaran, 'subtract');

        return response()->json([
            'message' => 'Pengeluaran berhasil ditambahkan',
            'data' => $pengeluaran
        ], 201);
    }

    public function show(Pengeluaran $pengeluaran)
    {
        return response()->json(['data' => $pengeluaran->load('user')]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pengeluaran $pengeluaran)
    {
        $validator = Validator::make($request->all(), [
            'keperluan' => 'sometimes|required|string|max:255',
            'jumlah_pengeluaran' => 'sometimes|required|numeric|min:0',
            'tanggal' => 'sometimes|required|date',
            'deskripsi' => 'sometimes|required|string',
            'nota' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle jumlah_pengeluaran change
        if ($request->has('jumlah_pengeluaran')) {
            $difference = $request->jumlah_pengeluaran - $pengeluaran->jumlah_pengeluaran;
            $keuangan = KeuanganMasjid::first();
            
            // Check if the new amount is valid
            if (($keuangan->saldo + $pengeluaran->jumlah_pengeluaran) < $request->jumlah_pengeluaran) {
                return response()->json(['error' => 'Saldo keuangan masjid tidak mencukupi untuk pengeluaran yang diubah'], 400);
            }

            // Adjust the keuangan masjid
            if ($difference != 0) {
                KeuanganMasjid::updateSaldo(abs($difference), $difference > 0 ? 'subtract' : 'add');
            }
        }

        $data = [
            'keperluan' => $request->keperluan ?? $pengeluaran->keperluan,
            'jumlah_pengeluaran' => $request->jumlah_pengeluaran ?? $pengeluaran->jumlah_pengeluaran,
            'tanggal' => $request->tanggal ?? $pengeluaran->tanggal,
            'deskripsi' => $request->deskripsi ?? $pengeluaran->deskripsi
        ];

        if ($request->hasFile('nota')) {
            // Delete old nota if exists
            if ($pengeluaran->nota) {
                $oldNota = str_replace('storage/', 'public/', $pengeluaran->nota);
                Storage::delete($oldNota);
            }

            $file = $request->file('nota');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/nota', $filename);
            $data['nota'] = 'storage/nota/' . $filename;
        }

        $pengeluaran->update($data);

        return response()->json(['data' => $pengeluaran]);
    }

    public function destroy($id): JsonResponse
    {
        $pengeluaran = Pengeluaran::find($id);
        
        if (!$pengeluaran) {
            return response()->json(['message' => 'Pengeluaran tidak ditemukan'], 404);
        }

        // Delete nota if exists
        if ($pengeluaran->nota) {
            $notaPath = str_replace('storage/', 'public/', $pengeluaran->nota);
            Storage::delete($notaPath);
        }
        
        // Return the amount to keuangan masjid before deleting
        KeuanganMasjid::updateSaldo($pengeluaran->jumlah_pengeluaran, 'add');

        $pengeluaran->delete();

        return response()->json(['message' => 'Pengeluaran berhasil dihapus']);
    }

    public function totalPengeluaran()
    {
        $total = Pengeluaran::sum('jumlah_pengeluaran');
        return response()->json(['total_pengeluaran' => $total]);
    }
}