<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\PasswordOtp;

class ForgotPasswordController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Hapus OTP lama
        PasswordOtp::where('email', $request->email)->delete();

        $otp = rand(100000, 999999);

        PasswordOtp::create([
            'email' => $request->email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Kirim Email (gunakan queue untuk produksi)
        Mail::raw("Kode OTP Anda adalah: $otp", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Kode OTP Reset Password');
        });

        return response()->json(['message' => 'OTP berhasil dikirim ke email.']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $otpRecord = PasswordOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'OTP tidak valid atau sudah kedaluwarsa.'], 422);
        }

        return response()->json(['message' => 'OTP valid.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
            'password' => 'required|min:6|confirmed'
        ]);

        $otpRecord = PasswordOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$otpRecord) {
            return response()->json(['message' => 'OTP tidak valid atau sudah kedaluwarsa.'], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => 'Pengguna tidak ditemukan.'], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Hapus OTP setelah digunakan
        PasswordOtp::where('email', $request->email)->delete();

        return response()->json(['message' => 'Password berhasil direset.']);
    }
}
