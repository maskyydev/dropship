<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Subscription;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AdminPanelController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function dashboard()
    {
        $users = User::all();
        $subscriptions = Subscription::with('user')->get();
        $payments = Payment::with(['user', 'subscription'])->get();
        
        return view('admin.dashboard', compact('users', 'subscriptions', 'payments'));
    }

    public function editUser(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function updateUser(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:admin,user',
            'is_subscribed' => 'boolean',
        ]);

        $user->update($validated);

        return redirect()->route('admin.dashboard')->with('success', 'User updated successfully');
    }

    public function deleteUser(User $user)
    {
        $user->delete();
        return back()->with('success', 'User deleted successfully');
    }
}