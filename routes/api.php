<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DonasiController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\SholatController;
use App\Http\Controllers\Api\RegisterController;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\AuthController;


// Public routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'registerPublic']); // Untuk user biasa

// Protected routes by Auth
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Registrasi khusus admin
    Route::middleware(['can:admin'])->group(function () {
        Route::post('/admin/register', [AuthController::class, 'registerByAdmin']);
        Route::get('/users', function () {
            return \App\Models\User::all();
        });
    });
});


