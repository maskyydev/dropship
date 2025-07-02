<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerificationNoticeController extends Controller
{
    public function show(Request $request)
    {
        // Dapatkan ID admin dari session
        $adminId = $request->session()->get('unverified_admin_id');
        
        if (!$adminId) {
            return redirect()->route('login');
        }

        return view('auth.admin-verification-notice', [
            'email' => \App\Models\User::find($adminId)->email
        ]);
    }
}