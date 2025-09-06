<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Subscription;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Customer;

class PlanController extends Controller
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function index()
    {
        $user = Auth::user();
        $subscription = $user->subscriptions()->latest()->first();
        
        return view('plans.index', compact('subscription'));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan' => 'required|in:monthly,yearly,free'
        ]);

        $user = Auth::user();
        $plan = $request->plan;

        // Check if user already has an active subscription
        $existingSubscription = $user->subscriptions()
            ->whereIn('status', ['active', 'trialing', 'incomplete'])
            ->first();

        if ($existingSubscription && $existingSubscription->isActive()) {
            return redirect()->route('plans.index')
                ->with('error', 'You already have an active subscription.');
        }

        // Handle free plan
        if ($plan === 'free') {
            $user->update([
                'current_plan' => 'free',
                'plan_expires_at' => now()->addDays(14),
            ]);

            return redirect()->route('chat.index')
                ->with('success', 'Free plan activated! You have 14 days with 50 messages per day.');
        }

        // Create or retrieve Stripe customer
        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->full_name,
            ]);
            
            $user->update(['stripe_customer_id' => $customer->id]);
        }

        // Define plan prices using your actual Stripe Price IDs
        $priceIds = [
            'monthly' => 'price_1RusIiCX5JXse2FtQAJkvc8l', // Your monthly price ID
            'yearly' => 'price_1RusKPCX5JXse2Fty1XiEvzq',  // Your yearly price ID
        ];

        // Create pending subscription record
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'stripe_customer_id' => $user->stripe_customer_id,
            'plan_type' => $plan,
            'status' => 'incomplete',
            'amount' => $plan === 'monthly' ? 1700 : 10000, // $17 or $100
            'currency' => 'usd',
        ]);

        // Create Stripe Checkout Session
        $session = Session::create([
            'customer' => $user->stripe_customer_id,
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $priceIds[$plan],
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => route('plans.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('plans.index'),
            'metadata' => [
                'user_id' => $user->id,
                'plan_type' => $plan,
                'subscription_id' => $subscription->id,
            ],
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');
        
        if (!$sessionId) {
            return redirect()->route('plans.index')
                ->with('error', 'Invalid session.');
        }

        try {
            $session = Session::retrieve($sessionId);
            $user = Auth::user();
            
            // Find the subscription record we created
            $subscription = Subscription::find($session->metadata->subscription_id);
            
            if ($subscription && $subscription->user_id === $user->id) {
                // Update subscription with Stripe data
                $stripeSubscription = \Stripe\Subscription::retrieve($session->subscription);
                
                // Safely handle timestamps - check if they exist and are not null
                $currentPeriodStart = null;
                $currentPeriodEnd = null;
                
                if (isset($stripeSubscription->current_period_start) && $stripeSubscription->current_period_start !== null) {
                    $currentPeriodStart = Carbon::createFromTimestamp($stripeSubscription->current_period_start);
                }
                
                if (isset($stripeSubscription->current_period_end) && $stripeSubscription->current_period_end !== null) {
                    $currentPeriodEnd = Carbon::createFromTimestamp($stripeSubscription->current_period_end);
                }
                
                $subscription->update([
                    'stripe_subscription_id' => $stripeSubscription->id,
                    'status' => $stripeSubscription->status,
                    'current_period_start' => $currentPeriodStart,
                    'current_period_end' => $currentPeriodEnd,
                ]);

                // Update user's plan
                $user->update([
                    'current_plan' => $subscription->plan_type,
                    'plan_expires_at' => $currentPeriodEnd ?? now()->addMonth(), // Fallback if end date is null
                ]);

                return redirect()->route('chat.index')
                    ->with('success', 'Subscription activated successfully!');
            }

            return redirect()->route('plans.index')
                ->with('error', 'Subscription not found.');
                
        } catch (\Exception $e) {
            return redirect()->route('plans.index')
                ->with('error', 'Error processing subscription: ' . $e->getMessage());
        }
    }

    public function cancel(Request $request)
    {
        $user = Auth::user();
        $subscription = $user->subscriptions()->where('status', 'active')->first();

        if (!$subscription) {
            return redirect()->route('plans.index')
                ->with('error', 'No active subscription found.');
        }

        try {
            // Cancel at period end in Stripe
            $stripeSubscription = \Stripe\Subscription::update(
                $subscription->stripe_subscription_id,
                ['cancel_at_period_end' => true]
            );

            // Update local subscription
            $subscription->update([
                'status' => 'cancel_at_period_end'
            ]);

            return redirect()->route('plans.index')
                ->with('success', 'Subscription will be canceled at the end of the current period.');
                
        } catch (\Exception $e) {
            return redirect()->route('plans.index')
                ->with('error', 'Error canceling subscription: ' . $e->getMessage());
        }
    }

    public function downgrade(Request $request)
    {
        $user = Auth::user();
        
        // Cancel any active subscriptions
        $activeSubscription = $user->subscriptions()->where('status', 'active')->first();
        
        if ($activeSubscription) {
            try {
                \Stripe\Subscription::update(
                    $activeSubscription->stripe_subscription_id,
                    ['cancel_at_period_end' => true]
                );
                
                $activeSubscription->update(['status' => 'cancel_at_period_end']);
            } catch (\Exception $e) {
                // Log error but continue with downgrade
            }
        }

        $user->update([
            'current_plan' => 'free',
            'plan_expires_at' => now()->addDays(14),
        ]);

        return redirect()->route('plans.index')
            ->with('success', 'Plan downgraded to free tier.');
    }
}