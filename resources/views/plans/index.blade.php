<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Choose Your Plan - {{ config('app.name') }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    :root {
        --primary-bg: #0f0f23;
        --secondary-bg: #171717;
        --accent-bg: #2d2d2d;
        --border-color: #565869;
        --text-primary: #ffffff;
        --text-secondary: #c5c5d2;
        --text-muted: #8e8ea0;
        --accent-color: #10a37f;
        --success-color: #4ade80;
        --error-color: #ef4444;
        --hover-bg: #343541;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: var(--primary-bg);
        color: var(--text-primary);
        min-height: 100vh;
        padding: 40px 20px;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .alert {
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alert.success {
        background: rgba(74, 222, 128, 0.1);
        border: 1px solid var(--success-color);
        color: var(--success-color);
    }

    .alert.error {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid var(--error-color);
        color: var(--error-color);
    }

    .current-subscription {
        background: var(--secondary-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 40px;
    }

    .current-subscription h2 {
        color: var(--accent-color);
        margin-bottom: 16px;
        font-size: 1.3rem;
    }

    .subscription-details {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 16px;
        margin-bottom: 20px;
    }

    .subscription-details>div {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .subscription-details .label {
        color: var(--text-muted);
        font-size: 12px;
        text-transform: uppercase;
        font-weight: 600;
    }

    .subscription-details .value {
        color: var(--text-primary);
        font-weight: 500;
    }

    .subscription-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .header {
        text-align: center;
        margin-bottom: 60px;
    }

    .header h1 {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 16px;
        color: var(--text-primary);
    }

    .header p {
        font-size: 1.1rem;
        color: var(--text-secondary);
    }

    .plans-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 30px;
        margin-bottom: 40px;
    }

    .plan-card {
        background: var(--secondary-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 32px;
        position: relative;
        transition: all 0.3s ease;
    }

    .plan-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .plan-card.popular {
        border-color: var(--accent-color);
        box-shadow: 0 0 0 1px var(--accent-color);
    }

    .plan-card.popular::before {
        content: 'Most Popular';
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        background: var(--accent-color);
        color: white;
        padding: 6px 16px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .plan-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        margin-bottom: 20px;
    }

    .plan-icon.free {
        background: linear-gradient(135deg, #6b7280, #9ca3af);
    }

    .plan-icon.monthly {
        background: linear-gradient(135deg, var(--accent-color), #0d8a6b);
    }

    .plan-icon.yearly {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    }

    .plan-name {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .plan-description {
        color: var(--text-secondary);
        font-size: 14px;
        margin-bottom: 20px;
    }

    .plan-price {
        margin-bottom: 24px;
    }

    .price-amount {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--text-primary);
    }

    .price-period {
        color: var(--text-muted);
        font-size: 14px;
        margin-left: 8px;
    }

    .plan-features {
        list-style: none;
        margin-bottom: 32px;
    }

    .plan-features li {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 0;
        color: var(--text-secondary);
        font-size: 14px;
    }

    .plan-features li i {
        color: var(--success-color);
        font-size: 16px;
        width: 16px;
    }

    .plan-button {
        width: 100%;
        padding: 14px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }

    .plan-button.primary {
        background: var(--accent-color);
        color: white;
    }

    .plan-button.primary:hover {
        background: #0d8a6b;
        transform: translateY(-1px);
    }

    .plan-button.secondary {
        background: transparent;
        color: var(--text-primary);
        border: 1px solid var(--border-color);
    }

    .plan-button.secondary:hover {
        background: var(--hover-bg);
        border-color: var(--accent-color);
    }

    .plan-button.current {
        background: var(--success-color);
        color: white;
        cursor: default;
    }

    .plan-button.cancel {
        background: var(--error-color);
        color: white;
    }

    .plan-button.cancel:hover {
        background: #dc2626;
    }

    .user-info {
        background: var(--secondary-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .user-details h3 {
        margin-bottom: 4px;
    }

    .user-details p {
        color: var(--text-secondary);
        font-size: 14px;
    }

    .logout-btn {
        background: transparent;
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .logout-btn:hover {
        background: var(--hover-bg);
        color: var(--text-primary);
    }

    @media (max-width: 768px) {
        .plans-container {
            grid-template-columns: 1fr;
        }

        .user-info {
            flex-direction: column;
            gap: 16px;
            text-align: center;
        }

        .subscription-actions {
            justify-content: center;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <!-- User Info -->
        <div class="user-info">
            <div class="user-details">
                <h3>Welcome, {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</h3>
                <p>{{ auth()->user()->email }}</p>
                @if(auth()->user()->current_plan)
                <p><strong>Current Plan:</strong> {{ ucfirst(auth()->user()->current_plan) }}</p>
                @if(auth()->user()->plan_expires_at)
                <p><strong>Expires:</strong> {{ auth()->user()->plan_expires_at->format('M d, Y') }}</p>
                @endif
                @endif
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>

        <!-- Alerts -->
        @if(session('success'))
        <div class="alert success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert error">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
        @endif

        <!-- Current Subscription Status -->
        @if($subscription && $subscription->isActive())
        <div class="current-subscription">
            <h2><i class="fas fa-crown"></i> Active Subscription</h2>
            <div class="subscription-details">
                <div>
                    <span class="label">Plan Type</span>
                    <span class="value">{{ ucfirst($subscription->plan_type) }}</span>
                </div>
                <div>
                    <span class="label">Status</span>
                    <span class="value">{{ ucfirst(str_replace('_', ' ', $subscription->status)) }}</span>
                </div>
                <div>
                    <span class="label">Current Period</span>
                    <span class="value">
                        {{ $subscription->current_period_start ? $subscription->current_period_start->format('M j') : 'N/A' }}
                        -
                        {{ $subscription->current_period_end ? $subscription->current_period_end->format('M j, Y') : 'N/A' }}
                    </span>
                </div>
                <div>
                    <span class="label">Amount</span>
                    <span class="value">${{ number_format($subscription->amount / 100, 2) }}
                        {{ strtoupper($subscription->currency) }}</span>
                </div>
            </div>

            <div class="subscription-actions">
                @if($subscription->status === 'active')
                <form action="{{ route('plans.cancel') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="plan-button cancel"
                        onclick="return confirm('Are you sure you want to cancel your subscription? It will remain active until the end of your billing period.')">
                        <i class="fas fa-times"></i> Cancel Subscription
                    </button>
                </form>
                @endif

                <form action="{{ route('plans.downgrade') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="plan-button secondary"
                        onclick="return confirm('Are you sure you want to downgrade to the free plan? You will lose access to premium features.')">
                        <i class="fas fa-level-down-alt"></i> Downgrade to Free
                    </button>
                </form>
            </div>
        </div>
        @endif

        <div class="header">
            <h1>Plans that grow with you</h1>
            <p>Choose the perfect plan for your AI assistant needs</p>
        </div>

        <div class="plans-container">
            <!-- Free Plan -->
            <div class="plan-card">
                <div class="plan-icon free">
                    <i class="fas fa-seedling"></i>
                </div>
                <h2 class="plan-name">Free</h2>
                <p class="plan-description">Perfect for getting started</p>
                <div class="plan-price">
                    <span class="price-amount">$0</span>
                    <span class="price-period">/ 2 weeks only</span>
                </div>
                <ul class="plan-features">
                    <li><i class="fas fa-check"></i> Basic AI conversations</li>
                    <li><i class="fas fa-check"></i> 50 messages per day</li>
                    <li><i class="fas fa-check"></i> Standard response time</li>
                    <li><i class="fas fa-check"></i> Basic chat history</li>
                    <li><i class="fas fa-check"></i> Community support</li>
                </ul>
                @if(auth()->user()->current_plan === 'free')
                <button class="plan-button current">
                    <i class="fas fa-check"></i> Current Plan
                </button>
                @else
                <form method="POST" action="{{ route('plans.subscribe') }}">
                    @csrf
                    <input type="hidden" name="plan" value="free">
                    <button type="submit" class="plan-button secondary">
                        <i class="fas fa-level-down-alt"></i> Switch to Free
                    </button>
                </form>
                @endif
            </div>

            <!-- Monthly Plan -->
            <div class="plan-card popular">
                <div class="plan-icon monthly">
                    <i class="fas fa-rocket"></i>
                </div>
                <h2 class="plan-name">Pro Monthly</h2>
                <p class="plan-description">For regular users and professionals</p>
                <div class="plan-price">
                    <span class="price-amount">$17</span>
                    <span class="price-period">/ month</span>
                </div>
                <ul class="plan-features">
                    <li><i class="fas fa-check"></i> Unlimited AI conversations</li>
                    <li><i class="fas fa-check"></i> Priority response time</li>
                    <li><i class="fas fa-check"></i> Advanced AI models</li>
                    <li><i class="fas fa-check"></i> Full chat history</li>
                    <li><i class="fas fa-check"></i> File uploads & analysis</li>
                    <li><i class="fas fa-check"></i> Email support</li>
                </ul>
                @if(auth()->user()->current_plan === 'monthly')
                <button class="plan-button current">
                    <i class="fas fa-check"></i> Current Plan
                </button>
                @else
                <form method="POST" action="{{ route('plans.subscribe') }}">
                    @csrf
                    <input type="hidden" name="plan" value="monthly">
                    <button type="submit" class="plan-button primary">
                        <i class="fas fa-credit-card"></i> Choose Monthly
                    </button>
                </form>
                @endif
            </div>

            <!-- Yearly Plan -->
            <div class="plan-card">
                <div class="plan-icon yearly">
                    <i class="fas fa-crown"></i>
                </div>
                <h2 class="plan-name">Pro Yearly</h2>
                <p class="plan-description">Best value for power users</p>
                <div class="plan-price">
                    <span class="price-amount">$100</span>
                    <span class="price-period">/ year</span>
                    <div style="color: var(--success-color); font-size: 12px; margin-top: 4px;">
                        Save $104 compared to monthly!
                    </div>
                </div>
                <ul class="plan-features">
                    <li><i class="fas fa-check"></i> Everything in Monthly</li>
                    <li><i class="fas fa-check"></i> 2 months free (save $34)</li>
                    <li><i class="fas fa-check"></i> Priority customer support</li>
                    <li><i class="fas fa-check"></i> Early access to new features</li>
                    <li><i class="fas fa-check"></i> Custom AI training</li>
                    <li><i class="fas fa-check"></i> API access</li>
                </ul>
                @if(auth()->user()->current_plan === 'yearly')
                <button class="plan-button current">
                    <i class="fas fa-check"></i> Current Plan
                </button>
                @else
                <form method="POST" action="{{ route('plans.subscribe') }}">
                    @csrf
                    <input type="hidden" name="plan" value="yearly">
                    <button type="submit" class="plan-button primary">
                        <i class="fas fa-crown"></i> Choose Yearly
                    </button>
                </form>
                @endif
            </div>
        </div>

        @if(auth()->user()->current_plan && auth()->user()->current_plan !== 'free')
        <div style="text-align: center; margin-top: 40px;">
            <a href="{{ route('chat.index') }}" class="plan-button primary"
                style="display: inline-block; max-width: 300px;">
                <i class="fas fa-comments"></i> Continue to Chat
            </a>
        </div>
        @endif
    </div>

    <script>
    // Handle form submissions with loading states
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            const button = this.querySelector('button[type="submit"]');
            if (button && !button.classList.contains('current')) {
                const originalContent = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
                button.disabled = true;

                // Re-enable after 10 seconds in case of error
                setTimeout(() => {
                    button.innerHTML = originalContent;
                    button.disabled = false;
                }, 10000);
            }
        });
    });
    </script>
</body>

</html>