<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Auth routes
        'api/login',
        'api/register',
        'api/logout',
        'api/admin/register',
        
        // User routes
        'api/users',
        'api/users/*',
        'api/profile',
        
        // Kegiatan routes
        'api/kegiatan',
        'api/kegiatan/*',
        
        // Donasi routes
        'api/donasi',
        'api/donasi/*',
        'api/donasi/statistics',
        'api/donasi/donors',
        'api/donasi/my-donations',
        '/donasi/total',
        'api/donasi/keuangan-masjid',
        
        // Pengeluaran routes
        'api/pengeluaran',
        'api/pengeluaran/*',
        
        // Sanctum routes
        'sanctum/csrf-cookie'
    ];
}
