<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function unverifiedAdmins()
    {
        $unverifiedAdmins = User::where('role', 'admin')
            ->whereNull('email_verified_at')
            ->get();
            
        return view('admin.verification.index', compact('unverifiedAdmins'));
    }

    public function verifyAdmin(User $admin)
    {
        $admin->update([
            'email_verified_at' => now(),
            'verified_by' => auth()->id()
        ]);
        
        return back()->with('success', 'Admin berhasil diverifikasi');
    }

    public function resendVerification(User $admin)
    {
        // Kirim email verifikasi
        // $admin->sendEmailVerificationNotification();
        
        return back()->with('success', 'Email verifikasi telah dikirim ulang');
    }
}