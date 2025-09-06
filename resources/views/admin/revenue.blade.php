<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Revenue Analytics - {{ config('app.name') }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        --danger-color: #ff6b6b;
        --success-color: #4ade80;
        --warning-color: #fbbf24;
        --info-color: #3b82f6;
        --hover-bg: #343541;
        --sidebar-width: 280px;
        --header-height: 70px;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: var(--primary-bg);
        color: var(--text-primary);
        min-height: 100vh;
    }

    /* Include sidebar styles (same as other pages) */
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        width: var(--sidebar-width);
        height: 100vh;
        background: var(--secondary-bg);
        border-right: 1px solid var(--border-color);
        z-index: 1000;
        overflow-y: auto;
        transition: transform 0.3s ease;
        display: flex;
        flex-direction: column;
    }

    .sidebar-header {
        padding: 20px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sidebar-logo {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        display: grid;
        place-items: center;
        color: white;
        font-size: 18px;
    }

    .sidebar-title {
        font-size: 18px;
        font-weight: 600;
    }

    .sidebar-nav {
        padding: 20px 0;
        flex: 1;
    }

    .nav-section {
        margin-bottom: 30px;
    }

    .nav-section-title {
        color: var(--text-muted);
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        padding: 0 20px;
        margin-bottom: 10px;
    }

    .nav-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        color: var(--text-secondary);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .nav-item:hover,
    .nav-item.active {
        background: var(--hover-bg);
        color: var(--text-primary);
    }

    .nav-item.active {
        border-right: 3px solid var(--accent-color);
    }

    .nav-item .icon {
        width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        opacity: 0.9;
        flex-shrink: 0;
    }

    .admin-user-section {
        position: sticky;
        bottom: 0;
        background: var(--accent-bg);
        border-top: 1px solid var(--border-color);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: auto;
    }

    .admin-user-info {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
        min-width: 0;
    }

    .admin-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--accent-color);
        display: grid;
        place-items: center;
        color: white;
        flex-shrink: 0;
    }

    .admin-details {
        flex: 1;
        min-width: 0;
    }

    .admin-name {
        font-size: 14px;
        font-weight: 600;
        line-height: 1.2;
    }

    .admin-email {
        color: var(--text-muted);
        font-size: 12px;
        line-height: 1.2;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .admin-actions {
        display: flex;
        gap: 8px;
    }

    .admin-action-btn {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: none;
        border: 1px solid var(--border-color);
        color: var(--text-muted);
        display: grid;
        place-items: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
    }

    .admin-action-btn:hover {
        background: var(--hover-bg);
        color: var(--text-primary);
        border-color: var(--accent-color);
    }

    .admin-action-btn.logout-btn:hover {
        background: rgba(255, 107, 107, 0.1);
        color: var(--danger-color);
        border-color: var(--danger-color);
    }

    .main-content {
        margin-left: var(--sidebar-width);
        min-height: 100vh;
    }

    .header {
        height: var(--header-height);
        background: var(--secondary-bg);
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 30px;
        position: sticky;
        top: 0;
        z-index: 100;
    }

    .page-title {
        font-size: 24px;
        font-weight: 600;
        color: var(--text-primary);
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: var(--accent-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-secondary);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .back-btn:hover {
        background: var(--hover-bg);
        color: var(--text-primary);
        border-color: var(--accent-color);
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    .revenue-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: var(--secondary-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 24px;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--accent-color), var(--info-color));
    }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .stat-title {
        color: var(--text-secondary);
        font-size: 14px;
        font-weight: 500;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: white;
    }

    .stat-icon.revenue {
        background: linear-gradient(135deg, #f093fb, #f5576c);
    }

    .stat-icon.monthly {
        background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .stat-icon.yearly {
        background: linear-gradient(135deg, #4facfe, #00f2fe);
    }

    .stat-icon.growth {
        background: linear-gradient(135deg, #43e97b, #38f9d7);
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .stat-change {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
    }

    .stat-change.positive {
        color: var(--success-color);
    }

    .stat-change.negative {
        color: var(--danger-color);
    }

    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }

    .card {
        background: var(--secondary-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .card-action {
        background: none;
        border: none;
        color: var(--accent-color);
        font-size: 14px;
        cursor: pointer;
        padding: 6px 10px;
        border-radius: 6px;
        transition: background 0.2s ease;
    }

    .card-action:hover {
        background: rgba(16, 163, 127, 0.1);
    }

    .card-content {
        padding: 24px;
    }

    .chart-container {
        height: 300px;
        position: relative;
    }

    .subscription-breakdown {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .subscription-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px;
        background: var(--accent-bg);
        border-radius: 8px;
        border: 1px solid var(--border-color);
    }

    .subscription-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .subscription-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        color: white;
    }

    .subscription-icon.free {
        background: linear-gradient(135deg, #6b7280, #9ca3af);
    }

    .subscription-icon.monthly {
        background: linear-gradient(135deg, var(--accent-color), #0d8a6b);
    }

    .subscription-icon.yearly {
        background: linear-gradient(135deg, var(--info-color), #1d4ed8);
    }

    .subscription-details h4 {
        font-weight: 600;
        margin-bottom: 2px;
    }

    .subscription-details p {
        color: var(--text-muted);
        font-size: 12px;
    }

    .subscription-stats {
        text-align: right;
    }

    .subscription-count {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-primary);
    }

    .subscription-revenue {
        color: var(--text-muted);
        font-size: 12px;
    }

    .revenue-table {
        background: var(--secondary-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .table-container {
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 16px 24px;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
    }

    th {
        background: var(--accent-bg);
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    tr:hover {
        background: var(--accent-bg);
    }

    .month-cell {
        font-weight: 600;
    }

    .revenue-cell {
        color: var(--success-color);
        font-weight: 600;
    }

    .growth-cell {
        font-size: 12px;
    }

    .growth-positive {
        color: var(--success-color);
    }

    .growth-negative {
        color: var(--danger-color);
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .main-content {
            margin-left: 0;
        }

        .container {
            padding: 20px 10px;
        }

        .revenue-stats {
            grid-template-columns: 1fr;
        }

        .card-header {
            padding: 16px;
        }

        .card-content {
            padding: 16px;
        }

        th,
        td {
            padding: 12px 16px;
        }
    }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-robot"></i>
            </div>
            <h2 class="sidebar-title">AI Assistant</h2>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Main</div>
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="icon">
                        <i class="fas fa-chart-line"></i>
                    </span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users') }}"
                    class="nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <span class="icon">
                        <i class="fas fa-users"></i>
                    </span>
                    <span>Users</span>
                </a>
                <a href="{{ route('admin.conversations') }}"
                    class="nav-item {{ request()->routeIs('admin.conversations') ? 'active' : '' }}">
                    <span class="icon">
                        <i class="fas fa-comments"></i>
                    </span>
                    <span>Conversations</span>
                </a>
                <a href="{{ route('admin.revenue') }}"
                    class="nav-item {{ request()->routeIs('admin.revenue') ? 'active' : '' }}">
                    <span class="icon">
                        <i class="fas fa-dollar-sign"></i>
                    </span>
                    <span>Revenue</span>
                </a>
            </div>
        </nav>

        <div class="admin-user-section">
            <div class="admin-user-info">
                <div class="admin-avatar">
                    {{ strtoupper(substr(auth()->user()->first_name ?? 'A', 0, 1)) }}
                </div>
                <div class="admin-details">
                    <div class="admin-name">{{ auth()->user()->first_name ?? 'Admin' }}
                        {{ auth()->user()->last_name ?? 'User' }}</div>
                    <div class="admin-email">{{ auth()->user()->email ?? 'admin@example.com' }}</div>
                </div>
            </div>
            <div class="admin-actions">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="admin-action-btn logout-btn" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main-content">
        <header class="header">
            <h1 class="page-title">Revenue Analytics</h1>
            <a href="{{ route('admin.dashboard') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
        </header>

        <main class="container">
            <div class="revenue-stats">
                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Total Revenue</div>
                            <div class="stat-value">${{ number_format($total_revenue ?? 0) }}</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i>
                                <span>+15.3% this month</span>
                            </div>
                        </div>
                        <div class="stat-icon revenue">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Monthly Revenue</div>
                            <div class="stat-value">${{ number_format($monthly_revenue ?? 0) }}</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i>
                                <span>+8.2% vs last month</span>
                            </div>
                        </div>
                        <div class="stat-icon monthly">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Yearly Revenue</div>
                            <div class="stat-value">${{ number_format($yearly_revenue ?? 0) }}</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i>
                                <span>+22.1% vs last year</span>
                            </div>
                        </div>
                        <div class="stat-icon yearly">
                            <i class="fas fa-crown"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Growth Rate</div>
                            <div class="stat-value">12.5%</div>
                            <div class="stat-change positive">
                                <i class="fas fa-arrow-up"></i>
                                <span>+2.1% this quarter</span>
                            </div>
                        </div>
                        <div class="stat-icon growth">
                            <i class="fas fa-trending-up"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-grid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Revenue Chart</h3>
                        <button class="card-action" type="button">Export Data</button>
                    </div>
                    <div class="card-content">
                        <div class="chart-container">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Subscription Breakdown</h3>
                        <button class="card-action" type="button">View Details</button>
                    </div>
                    <div class="card-content">
                        <div class="subscription-breakdown">
                            <div class="subscription-item">
                                <div class="subscription-info">
                                    <div class="subscription-icon free">
                                        <i class="fas fa-seedling"></i>
                                    </div>
                                    <div class="subscription-details">
                                        <h4>Free Plan</h4>
                                        <p>Trial users</p>
                                    </div>
                                </div>
                                <div class="subscription-stats">
                                    <div class="subscription-count">{{ $subscription_stats['free'] ?? 0 }}</div>
                                    <div class="subscription-revenue">$0/month</div>
                                </div>
                            </div>

                            <div class="subscription-item">
                                <div class="subscription-info">
                                    <div class="subscription-icon monthly">
                                        <i class="fas fa-rocket"></i>
                                    </div>
                                    <div class="subscription-details">
                                        <h4>Monthly Plan</h4>
                                        <p>$17/month</p>
                                    </div>
                                </div>
                                <div class="subscription-stats">
                                    <div class="subscription-count">{{ $subscription_stats['monthly'] ?? 0 }}</div>
                                    <div class="subscription-revenue">
                                        ${{ number_format(($subscription_stats['monthly'] ?? 0) * 17) }}/month</div>
                                </div>
                            </div>

                            <div class="subscription-item">
                                <div class="subscription-info">
                                    <div class="subscription-icon yearly">
                                        <i class="fas fa-crown"></i>
                                    </div>
                                    <div class="subscription-details">
                                        <h4>Yearly Plan</h4>
                                        <p>$100/year</p>
                                    </div>
                                </div>
                                <div class="subscription-stats">
                                    <div class="subscription-count">{{ $subscription_stats['yearly'] ?? 0 }}</div>
                                    <div class="subscription-revenue">
                                        ${{ number_format(($subscription_stats['yearly'] ?? 0) * 100) }}/year</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="revenue-table">
                <div class="card-header">
                    <h3 class="card-title">Monthly Revenue Breakdown</h3>
                    <button class="card-action" type="button">Export Report</button>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Monthly Subs</th>
                                <th>Yearly Subs</th>
                                <th>Total Revenue</th>
                                <th>Growth</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($monthly_revenue_data as $data)
                            <tr>
                                <td class="month-cell">{{ $data['month'] }}</td>
                                <td>{{ $data['monthly_subs'] }}</td>
                                <td>{{ $data['yearly_subs'] }}</td>
                                <td class="revenue-cell">${{ number_format($data['total_revenue']) }}</td>
                                <td
                                    class="growth-cell {{ $data['growth'] >= 0 ? 'growth-positive' : 'growth-negative' }}">
                                    <i class="fas fa-arrow-{{ $data['growth'] >= 0 ? 'up' : 'down' }}"></i>
                                    {{ abs($data['growth']) }}%
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    const revenueData = @json($revenue_chart_data);

    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: revenueData.map(item => item.month),
            datasets: [{
                label: 'Revenue ($)',
                data: revenueData.map(item => item.revenue),
                borderColor: '#10a37f',
                backgroundColor: 'rgba(16, 163, 127, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#10a37f',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)',
                        borderColor: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: '#8e8ea0'
                    }
                },
                y: {
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)',
                        borderColor: 'rgba(255, 255, 255, 0.1)'
                    },
                    ticks: {
                        color: '#8e8ea0',
                        callback: function(value) {
                            return '$' + value.toLocaleString();
                        }
                    }
                }
            },
            elements: {
                point: {
                    hoverBackgroundColor: '#10a37f'
                }
            }
        }
    });
    </script>
</body>

</html>