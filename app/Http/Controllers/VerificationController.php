<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerificationCodeMail;
use Illuminate\Support\Str;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string',
            'email' => 'required|email'
        ]);

        // Cek kode verifikasi di database atau session
        // Ini contoh sederhana, Anda mungkin perlu menyesuaikan dengan sistem Anda
        $storedCode = session('verification_code_'.$request->email);
        
        if ($request->verification_code === $storedCode) {
            // Update status verifikasi user
            $user = \App\Models\User::where('email', $request->email)->first();
            if ($user) {
                $user->email_verified_at = now();
                $user->save();
            }
            
            return back()->with('success', 'Email berhasil diverifikasi! Akun Anda sekarang sudah terverifikasi.');
        }

        return back()->with('error', 'Kode verifikasi tidak valid.');
    }

    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        // Generate kode verifikasi baru
        $verificationCode = Str::random(6);
        
        // Simpan kode di session (atau database)
        session(['verification_code_'.$request->email => $verificationCode]);
        
        // Kirim email
        Mail::to($request->email)->send(new VerificationCodeMail($verificationCode));

        return back()->with('success', 'Kode verifikasi baru telah dikirim ke email Anda.');
    }
}