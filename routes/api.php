<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DonasiController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\SholatController;
use App\Http\Controllers\Api\RegisterController;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


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

Route::post('/register', [RegisterController::class, 'register']); // Hanya warga bisa register
Route::post('/login', [RegisterController::class, 'login']);
Route::post('/logout', [RegisterController::class, 'logout'])->middleware('auth:api');

// ✅ RUTE API DENGAN MIDDLEWARE JWT
Route::middleware(['auth:api'])->group(function () {

    // ✅ Admin: Bisa akses semua
    Route::middleware('role:admin')->group(function () {
        Route::apiResource('users', UserController::class);
        Route::apiResource('kegiatan', KegiatanController::class);
        Route::apiResource('donasi', DonasiController::class);
        Route::apiResource('pengeluaran', PengeluaranController::class);
        Route::apiResource('sholat', SholatController::class);

        // ✅ ADMIN BISA BUAT TAKMIR
        Route::post('/register-takmir', function (Request $request) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'roles' => 'takmir', // ✅ Takmir hanya bisa dibuat oleh admin
            ]);

            return response()->json(['message' => 'Takmir registered successfully'], 201);
        });
    });

    // ✅ Takmir: Bisa akses donasi, pengeluaran, sholat
    Route::middleware('role:takmir')->group(function () {
        Route::apiResource('donasi', DonasiController::class)->only(['index', 'store']);
        Route::apiResource('pengeluaran', PengeluaranController::class)->only(['index', 'store']);
        Route::apiResource('sholat', SholatController::class)->only(['index']);
    });

    // ✅ Warga: Hanya bisa melihat kegiatan, sholat, dan donasi
    Route::middleware('role:warga')->group(function () {
        Route::get('kegiatan', [KegiatanController::class, 'index']);
        Route::get('sholat', [SholatController::class, 'index']);
        Route::get('donasi', [DonasiController::class, 'index']);
    });
});

// Route test API
Route::get('/test', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'API is working properly',
        'timestamp' => now()
    ]);
});

// Route::prefix('kegiatan')->group(function () {
//     Route::get('/', [KegiatanController::class, 'index']);              // Get semua kegiatan
//     Route::post('/', [KegiatanController::class, 'store']);             // Membuat kegiatan baru
//     Route::get('/{id}', [KegiatanController::class, 'show']);          // Get detail kegiatan
//     Route::put('/{id}', [KegiatanController::class, 'update']);        // Update kegiatan
//     Route::delete('/{id}', [KegiatanController::class, 'destroy']);    // Hapus kegiatan
// });

// Route::prefix('users')->group(function () {
//     Route::get('/', [UserController::class, 'index']);          // Get semua user
//     Route::post('/', [UserController::class, 'store']);         // Membuat user baru
//     Route::get('/{user}', [UserController::class, 'show']);     // Get detail user
//     Route::put('/{user}', [UserController::class, 'update']);   // Update user
//     Route::delete('/{user}', [UserController::class, 'destroy']); // Hapus user
// });
// Route::prefix('donasi')->group(function () {
//     Route::get('/', [DonasiController::class, 'index']);          // Get semua donasi
//     Route::post('/', [DonasiController::class, 'store']);         // Membuat data donasi baru
//     // Route::get('/{donasi}', [DonasiController::class, 'show']);     // Get detail donasi
// });
// Route::prefix('pengeluaran')->group(function () {
//     Route::get('/', [PengeluaranController::class, 'index']);          // Get semua pengeluaran
//     Route::post('/', [PengeluaranController::class, 'store']);         // Membuat data pengeluaran baru
//     Route::get('/{id}', [PengeluaranController::class, 'show']);     // Get detail pengeluaran
//     Route::put('/{id}', [PengeluaranController::class, 'update']);   // Update pengeluaran
// });
// Route::prefix('sholat')->group(function () {
//     Route::get('/', [SholatController::class, 'index']);          // Get semua data jadwal sholat
//     Route::post('/', [SholatController::class, 'store']);         // Membuat data jadwal sholat baru
//     Route::get('/{id}', [SholatController::class, 'show']);     // Get detail datajadwal sholat
// });


