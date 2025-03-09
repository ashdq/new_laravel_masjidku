<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KegiatanController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route test API
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API is working properly',
        'timestamp' => now()
    ]);
});

Route::prefix('kegiatan')->group(function () {
    Route::get('/', [KegiatanController::class, 'index']);              // Get semua kegiatan
    Route::post('/', [KegiatanController::class, 'store']);             // Membuat kegiatan baru
    Route::get('/{id}', [KegiatanController::class, 'show']);          // Get detail kegiatan
    Route::put('/{id}', [KegiatanController::class, 'update']);        // Update kegiatan
    Route::delete('/{id}', [KegiatanController::class, 'destroy']);    // Hapus kegiatan
});


