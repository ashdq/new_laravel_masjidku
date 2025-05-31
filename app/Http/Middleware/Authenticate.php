<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        // return $request->expectsJson() ? null : route('login');
        // Untuk request API/JSON, kembalikan null
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }
        
        // Jika tidak ada route login, kembalikan response JSON
        if (!Route::has('login')) {
            abort(response()->json([
                'message' => 'Unauthenticated',
                'success' => false
            ], 401));
        }
        
        return route('login');
    }
}
