<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class VerifyEmailController extends Controller
{
    public function __invoke(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);
        
        if (!hash_equals($hash, sha1($user->email))) {
            abort(403);
        }

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        return response()->json(['message' => 'Email verified successfully']);
    }
}
