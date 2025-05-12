<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\KegiatanController;
use App\Http\Controllers\Api\DonasiController;
use App\Http\Controllers\Api\PengeluaranController;
use Illuminate\Support\Facades\Hash;


// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'registerPublic']); // Untuk user biasa

// Protected routes by Auth
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // User profile management (for all authenticated users)
    Route::put('/profile', [UserController::class, 'updateProfile']);
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


