<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_subscribed',
        'subscription_expiry'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'subscription_expiry' => 'datetime',
        'is_subscribed' => 'boolean'
    ];

    public static $roles = [
        'admin' => 'Administrator',
        'user' => 'Regular User',
        'manager' => 'Manager'
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function hasActiveSubscription()
    {
        return $this->is_subscribed && $this->subscription_expiry->isFuture();
    }
}