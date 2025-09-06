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
        'first_name',
        'last_name',
        'email',
        'password',
        'current_plan',
        'plan_expires_at',
        'stripe_customer_id',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'plan_expires_at' => 'datetime',
        'is_admin' => 'boolean',
    ];

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }


    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function hasActivePlan()
    {
        if ($this->current_plan === 'free') {
            return $this->plan_expires_at && $this->plan_expires_at->isFuture();
        }
        
        return $this->current_plan && $this->plan_expires_at && $this->plan_expires_at->isFuture();
    }

    public function canSendMessage()
    {
        if (!$this->hasActivePlan()) {
            return false;
        }

        // Check daily message limits for free plan
        if ($this->current_plan === 'free') {
            $todayMessages = $this->messages()
                ->whereDate('created_at', today())
                ->count();
            
            return $todayMessages < 50; // Free plan limit
        }

        return true; // Paid plans have unlimited messages
    }

    public function isAdmin()
    {
        return $this->is_admin;
    }
    public function subscriptions()
{
    return $this->hasMany(Subscription::class);
}

public function activeSubscription()
{
    return $this->hasOne(Subscription::class)->whereIn('status', ['active', 'trialing']);
}

public function hasActiveSubscription()
{
    return $this->activeSubscription()->exists();
}
}