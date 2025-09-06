<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Subscription;
use Stripe\Webhook;
use Carbon\Carbon;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook.secret'); // Updated to match your config

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            Log::error('Invalid payload: ' . $e->getMessage());
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error('Webhook signature verification failed: ' . $e->getMessage());
            return response('Invalid signature', 400);
        }

        // Handle the event
        switch ($event['type']) {
            case 'checkout.session.completed':
                $this->handleCheckoutSessionCompleted($event['data']['object']);
                break;
                
            case 'customer.subscription.created':
            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event['data']['object']);
                break;
                
            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event['data']['object']);
                break;
                
            case 'invoice.payment_succeeded':
                $this->handlePaymentSucceeded($event['data']['object']);
                break;
                
            case 'invoice.payment_failed':
                $this->handlePaymentFailed($event['data']['object']);
                break;
                
            default:
                Log::info('Unhandled webhook event: ' . $event['type']);
        }

        return response('Webhook handled', 200);
    }

    private function handleCheckoutSessionCompleted($session)
    {
        if ($session['mode'] === 'subscription' && isset($session['metadata']['subscription_id'])) {
            $subscription = Subscription::find($session['metadata']['subscription_id']);
            
            if ($subscription) {
                // The subscription will be updated via the subscription.created/updated webhook
                // This just logs the successful checkout
                Log::info('Checkout session completed for subscription: ' . $subscription->id);
            }
        }
    }

    private function handleSubscriptionUpdated($stripeSubscription)
    {
        $user = User::where('stripe_customer_id', $stripeSubscription['customer'])->first();
        
        if (!$user) {
            Log::error('User not found for customer: ' . $stripeSubscription['customer']);
            return;
        }

        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription['id'])
            ->orWhere(function($query) use ($user, $stripeSubscription) {
                $query->where('user_id', $user->id)
                      ->where('status', 'incomplete')
                      ->whereNull('stripe_subscription_id');
            })
            ->first();

        if (!$subscription) {
            // Create new subscription record if not exists
            $subscription = new Subscription([
                'user_id' => $user->id,
                'stripe_customer_id' => $stripeSubscription['customer'],
            ]);
        }

        // Update subscription with Stripe data
        $subscription->fill([
            'stripe_subscription_id' => $stripeSubscription['id'],
            'status' => $stripeSubscription['status'],
            'current_period_start' => Carbon::createFromTimestamp($stripeSubscription['current_period_start']),
            'current_period_end' => Carbon::createFromTimestamp($stripeSubscription['current_period_end']),
            'amount' => $stripeSubscription['items']['data'][0]['price']['unit_amount'] ?? 0,
            'currency' => $stripeSubscription['items']['data'][0]['price']['currency'] ?? 'usd',
            'plan_type' => $this->determinePlanType($stripeSubscription),
        ]);

        $subscription->save();

        // Update user's current plan if subscription is active
        if (in_array($subscription->status, ['active', 'trialing'])) {
            $user->update([
                'current_plan' => $subscription->plan_type,
                'plan_expires_at' => $subscription->current_period_end,
            ]);
        }

        Log::info('Subscription updated: ' . $subscription->id . ' Status: ' . $subscription->status);
    }

    private function handleSubscriptionDeleted($stripeSubscription)
    {
        $subscription = Subscription::where('stripe_subscription_id', $stripeSubscription['id'])->first();
        
        if ($subscription) {
            $subscription->update(['status' => 'canceled']);
            
            // Update user to free plan with 14-day trial
            $subscription->user->update([
                'current_plan' => 'free',
                'plan_expires_at' => now()->addDays(14),
            ]);

            Log::info('Subscription canceled: ' . $subscription->id);
        }
    }

    private function handlePaymentSucceeded($invoice)
    {
        if ($invoice['subscription']) {
            $subscription = Subscription::where('stripe_subscription_id', $invoice['subscription'])->first();
            
            if ($subscription) {
                // Update subscription status to active on successful payment
                $subscription->update(['status' => 'active']);
                
                $subscription->user->update([
                    'current_plan' => $subscription->plan_type,
                    'plan_expires_at' => $subscription->current_period_end,
                ]);

                Log::info('Payment succeeded for subscription: ' . $subscription->id);
            }
        }
    }

    private function handlePaymentFailed($invoice)
    {
        if ($invoice['subscription']) {
            $subscription = Subscription::where('stripe_subscription_id', $invoice['subscription'])->first();
            
            if ($subscription) {
                $subscription->update(['status' => 'past_due']);
                Log::warning('Payment failed for subscription: ' . $subscription->id);
            }
        }
    }

    private function determinePlanType($stripeSubscription)
    {
        $interval = $stripeSubscription['items']['data'][0]['price']['recurring']['interval'] ?? 'month';
        return $interval === 'year' ? 'yearly' : 'monthly';
    }
}