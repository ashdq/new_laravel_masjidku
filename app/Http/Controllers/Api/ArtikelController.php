<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Artikel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArtikelController extends Controller
{
    public function index()
    {
        $artikels = Artikel::latest()->get();
        return response()->json([
            'status' => 'success',
            'data' => $artikels
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'judul' => 'required|string|max:255',
            'gambar_artikel' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'sumber' => 'required|string|max:255',
            'isi_artikel' => 'required|string',
            'tanggal_artikel' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 422);
        }

        $data = $request->all();

        if ($request->hasFile('gambar_artikel')) {
            $file = $request->file('gambar_artikel');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/artikel', $filename);
            $data['gambar_artikel'] = 'storage/artikel/' . $filename;
        }

        $artikel = Artikel::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Artikel berhasil ditambahkan',
            'data' => $artikel
        ], 201);
    }

    public function show($id)
    {
        $artikel = Artikel::find($id);
        
        if (!$artikel) {
            return response()->json([
                'status' => 'error',
                'message' => 'Artikel tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $artikel
        ]);
    }

    public function update(Request $request, $id)
    {
        $artikel = Artikel::find($id);
        
        if (!$artikel) {
            return response()->json([
                'status' => 'error',
                'message' => 'Artikel tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'judul' => 'string|max:255',
            'gambar_artikel' => 'image|mimes:jpeg,png,jpg|max:2048',
            'sumber' => 'string|max:255',
            'isi_artikel' => 'string',
            'tanggal_artikel' => 'date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validator->errors()
            ], 422);
        }

        $data = $request->all();

        if ($request->hasFile('gambar_artikel')) {
            // Hapus gambar lama jika ada
            if ($artikel->gambar_artikel) {
                $oldImage = str_replace('storage/', 'public/', $artikel->gambar_artikel);
                Storage::delete($oldImage);
            }

            $file = $request->file('gambar_artikel');
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/artikel', $filename);
            $data['gambar_artikel'] = 'storage/artikel/' . $filename;
        }

        $artikel->update($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Artikel berhasil diperbarui',
            'data' => $artikel
        ]);
    }

    public function destroy($id)
    {
        $artikel = Artikel::find($id);
        
        if (!$artikel) {
            return response()->json([
                'status' => 'error',
                'message' => 'Artikel tidak ditemukan'
            ], 404);
        }

        // Hapus gambar jika ada
        if ($artikel->gambar_artikel) {
            $imagePath = str_replace('storage/', 'public/', $artikel->gambar_artikel);
            Storage::delete($imagePath);
        }

        $artikel->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Artikel berhasil dihapus'
        ]);
    }
}
