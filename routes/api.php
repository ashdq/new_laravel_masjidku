<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\KegiatanController;
use App\Http\Controllers\Api\DonasiController;
use App\Http\Controllers\Api\PengeluaranController;
use App\Http\Controllers\Api\KeuanganController;
use App\Http\Controllers\Api\AspirasiController;
use App\Http\Controllers\Api\ArtikelController;
use App\Http\Controllers\Api\VerifyEmailController;
use App\Http\Controllers\Api\ForgotPasswordController;


// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'registerPublic']); // Untuk user biasa
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed'])
        ->name('verification.verify');
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return response()->json(['status' => 'verification-link-sent']);
})->middleware(['throttle:6,1'])->name('verification.send');
Route::get('/kegiatan/download/{filename}', [KegiatanController::class, 'download']);
Route::post('/forgot-password/send-otp', [ForgotPasswordController::class, 'sendOtp']);
Route::post('/forgot-password/verify-otp', [ForgotPasswordController::class, 'verifyOtp']);
Route::post('/forgot-password/reset', [ForgotPasswordController::class, 'resetPassword']);

// Protected routes by Auth
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // User profile management (for all authenticated users)
    Route::put('/users/change-password', [UserController::class, 'changePassword']);
    Route::delete('/profile', [UserController::class, 'deleteOwnAccount']);
    
    // Kegiatan management (for authenticated users)
    Route::apiResource('kegiatan', KegiatanController::class);
    

    // Donasi routes - Specific routes first
    Route::get('/donasi/statistics', [DonasiController::class, 'getStatistics']);
    Route::get('/donasi/donors', [DonasiController::class, 'getDonors']);
    Route::get('/donasi/my-donations', [DonasiController::class, 'getMyDonations']);
    
    // Donasi general routes
    Route::get('/donasi', [DonasiController::class, 'index']);
    Route::post('/donasi', [DonasiController::class, 'store']);
    Route::get('/donasi/{donasi}', [DonasiController::class, 'show']);
    
    // Get total donasi (new route)
    Route::get('/donasi-total', [DonasiController::class, 'getTotalDonasi']);

    // Pengeluaran routes
    Route::get('/pengeluaran', [PengeluaranController::class, 'index']);
    Route::post('/pengeluaran', [PengeluaranController::class, 'store']);
    Route::get('/pengeluaran/{pengeluaran}', [PengeluaranController::class, 'show']);
    Route::put('/pengeluaran/{pengeluaran}', [PengeluaranController::class, 'update']);
    Route::delete('/pengeluaran/{pengeluaran}', [PengeluaranController::class, 'destroy']);
    
    // Get total pengeluaran (new route)
    Route::get('/pengeluaran-total', [PengeluaranController::class, 'totalPengeluaran']);

    // Keuangan routes
    Route::get('/keuangan', [KeuanganController::class, 'index']);
    Route::get('/keuangan/saldo', [KeuanganController::class, 'saldo']);

    // Aspirasi routes
    Route::post('/aspirasi', [AspirasiController::class, 'store']); // warga kirim aspirasi
    Route::get('/aspirasi', [AspirasiController::class, 'index']); // admin & takmir lihat semua
    Route::delete('/aspirasi/{id}', [AspirasiController::class, 'destroy']); // admin & takmir hapus aspirasi

    //Artikel routes
    Route::get('/artikel', [ArtikelController::class, 'index']);
    Route::post('/artikel', [ArtikelController::class, 'store']);
    Route::get('/artikel/{id}', [ArtikelController::class, 'show']);
    Route::put('/artikel/{id}', [ArtikelController::class, 'update']);
    Route::delete('/artikel/{id}', [ArtikelController::class, 'destroy']);


    // Admin only routes
    Route::middleware(['can:admin'])->group(function () {
        // User registration by admin
        Route::post('/admin/register', [AuthController::class, 'registerByAdmin']);
        
        // User management
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}', [UserController::class, 'update']);
        Route::delete('/users/{user}', [UserController::class, 'destroy']);
    });
});


