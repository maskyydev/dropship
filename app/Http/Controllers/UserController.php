<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription; // ⬅ WAJIB
use App\Models\Payment;      // ⬅ WAJIB
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()
                    ->with(['subscriptions', 'payments'])
                    ->paginate(10);

        $recentSubscriptions = Subscription::with('user')
                                ->latest()
                                ->take(5)
                                ->get();

        $recentPayments = Payment::with('user')
                            ->latest()
                            ->take(5)
                            ->get();

        return view('admin.users.index', compact('users', 'recentSubscriptions', 'recentPayments'));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
            'role' => 'required|in:admin,user,manager',
            'password' => 'nullable|string|min:8|confirmed',
            'is_subscribed' => 'boolean',
            'subscription_expiry' => 'nullable|date'
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('admin.users.index')
                         ->with('success', 'User updated successfully');
    }

    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return redirect()->back()
                           ->with('error', 'You cannot delete your own account');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
                         ->with('success', 'User deleted successfully');
    }
}