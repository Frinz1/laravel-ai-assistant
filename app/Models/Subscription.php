<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'stripe_subscription_id',
        'stripe_customer_id',
        'plan_type',
        'status',
        'current_period_start',
        'current_period_end',
        'amount',
        'currency',
    ];

    protected $casts = [
        'current_period_start' => 'datetime',
        'current_period_end' => 'datetime',
        'amount' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isActive()
    {
        return in_array($this->status, ['active', 'trialing']);
    }

    public function isExpired()
    {
        return $this->current_period_end && $this->current_period_end->isPast();
    }

    public function isIncomplete()
    {
        return $this->status === 'incomplete' || !$this->stripe_subscription_id;
    }

    public function daysUntilExpiry()
    {
        if (!$this->current_period_end) {
            return null;
        }
        
        return now()->diffInDays($this->current_period_end, false);
    }
}

// =================================