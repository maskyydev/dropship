<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;
use App\Mail\VerificationCodeMail;
use Carbon\Carbon;

class EmailVerificationController extends Controller
{
    /**
     * Verifikasi kode dari email
     */
    public function verify(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string',
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email tidak ditemukan.');
        }

        if ($user->email_verified_at) {
            return redirect('/dashboard')->with('success', 'Email sudah diverifikasi sebelumnya.');
        }

        if ($request->verification_code === $user->verification_code) {
            $user->email_verified_at = now();
            $user->verification_code = null;
            $user->save();

            // Redirect ke /dashboard dengan notifikasi modal
            return redirect()->back()->with('success', 'Email berhasil diverifikasi!');
        }

        return back()->with('error', 'Kode verifikasi tidak valid.');
    }

    /**
     * Kirim ulang kode verifikasi ke email
     */
    public function resend(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->with('error', 'Email tidak ditemukan.');
        }

        if ($user->email_verified_at) {
            return redirect('/dashboard')->with('success', 'Email sudah terverifikasi.');
        }

        // Generate kode acak 6 karakter
        $verificationCode = strtoupper(Str::random(6));

        $user->verification_code = $verificationCode;
        $user->save();

        try {
            Mail::to($user->email)->send(new VerificationCodeMail($verificationCode));
        } catch (\Exception $e) {
            Log::error('Gagal mengirim email verifikasi: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengirim email. Silakan coba lagi.');
        }

        return back()->with('success', 'Kode verifikasi baru telah dikirim ke email Anda.');
    }
}
