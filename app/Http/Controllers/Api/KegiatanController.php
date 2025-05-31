<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kegiatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KegiatanController extends Controller
{
    public function index()
    {
        $kegiatan = Kegiatan::select('id', 'nama_kegiatan', 'tanggal_kegiatan', 'waktu_kegiatan', 'gambar_kegiatan')
            ->orderBy('tanggal_kegiatan', 'desc')
            ->get()
            ->map(function ($item) {
                $item->waktu_kegiatan = date('H:i:s', strtotime($item->waktu_kegiatan));
                $item->tanggal_kegiatan = date('Y-m-d', strtotime($item->tanggal_kegiatan));
                return $item;
            });
        return response()->json(['data' => $kegiatan]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kegiatan' => 'required|string|max:255',
            'tanggal_kegiatan' => 'required|date',
            'waktu_kegiatan' => 'required',
            'gambar_kegiatan' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = $request->all();

        if ($request->hasFile('gambar_kegiatan')) {
            $file = $request->file('gambar_kegiatan');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $originalName = $file->getClientOriginalName(); // Simpan dengan ekstensi
            $file->storeAs('public/kegiatan', $filename);
            $data['gambar_kegiatan'] = $filename; // hanya nama file
            $data['original_filename'] = $originalName;
        }

        $kegiatan = Kegiatan::create($data);
        return response()->json([
            'message' => 'Kegiatan berhasil ditambahkan',
            'data' => $kegiatan
        ], 201);
    }

    public function download($filename)
    {
        $path = storage_path('app/public/kegiatan/' . $filename);

        if (!file_exists($path)) {
            return response()->json(['message' => 'File tidak ditemukan'], 404);
        }

        return response()->download($path);
    }

    public function show($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        return response()->json(['data' => $kegiatan]);
    }

    public function update(Request $request, $id)
    {
        $kegiatan = Kegiatan::findOrFail($id);

        // Validasi hanya field yang dikirim
        $validatedData = $request->validate([
            'nama_kegiatan' => 'sometimes|string|max:255',
            'tanggal_kegiatan' => 'sometimes|date',
            'waktu_kegiatan' => 'sometimes',
            'gambar_kegiatan' => 'sometimes|nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        // Jika gambar dikirim, handle upload
        if ($request->hasFile('gambar_kegiatan')) {
            // Hapus gambar lama jika ada
            if ($kegiatan->gambar_kegiatan) {
                $oldImage = str_replace('storage/', 'public/', $kegiatan->gambar_kegiatan);
                Storage::delete($oldImage);
            }

            $file = $request->file('gambar_kegiatan');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $originalName = $file->getClientOriginalName(); // Simpan dengan ekstensi
            $file->storeAs('public/kegiatan', $filename);
            $data['gambar_kegiatan'] = $filename; // hanya nama file
            $validatedData['original_filename'] = $originalName;
        }

        // Update hanya field yang dikirim
        $kegiatan->update($validatedData);

        return response()->json([
            'message' => 'Kegiatan berhasil diperbarui',
            'data' => $kegiatan
        ]);
    }

    public function destroy($id)
    {
        $kegiatan = Kegiatan::findOrFail($id);
        
        // Hapus gambar jika ada
        if ($kegiatan->gambar_kegiatan) {
            $imagePath = str_replace('storage/', 'public/', $kegiatan->gambar_kegiatan);
            Storage::delete($imagePath);
        }

        $kegiatan->delete();
        return response()->json([
            'message' => 'Kegiatan berhasil dihapus'
        ], 204);
    }
}
