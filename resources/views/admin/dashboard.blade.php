<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - {{ config('app.name') }}</title>
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
        overflow-x: hidden;
        line-height: 1.5;
    }

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

    .content {
        padding: 30px;
    }

    .dashboard-stats {
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
        gap: 16px;
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
        display: grid;
        place-items: center;
        font-size: 20px;
        color: white;
        flex-shrink: 0;
    }

    .stat-icon.users {
        background: linear-gradient(135deg, #667eea, #764ba2);
    }

    .stat-icon.revenue {
        background: linear-gradient(135deg, #f093fb, #f5576c);
    }

    .stat-icon.conversations {
        background: linear-gradient(135deg, #4facfe, #00f2fe);
    }

    .stat-icon.messages {
        background: linear-gradient(135deg, #43e97b, #38f9d7);
    }

    .stat-value {
        font-size: 32px;
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
        gap: 16px;
    }

    .card-title {
        font-size: 18px;
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
        text-decoration: none;
    }

    .card-action:hover {
        background: rgba(16, 163, 127, 0.1);
    }

    .card-content {
        padding: 24px;
    }

    .activity-list {
        max-height: 400px;
        overflow-y: auto;
    }

    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px 24px;
        border-bottom: 1px solid var(--border-color);
        transition: background 0.2s ease;
    }

    .activity-item:hover {
        background: var(--accent-bg);
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: var(--accent-color);
        display: grid;
        place-items: center;
        font-size: 14px;
        font-weight: 600;
        flex-shrink: 0;
        color: white;
    }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        color: var(--text-primary);
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 4px;
    }

    .activity-description {
        color: var(--text-muted);
        font-size: 13px;
        margin-bottom: 4px;
    }

    .activity-time {
        color: var(--text-muted);
        font-size: 12px;
    }

    .chart-container {
        height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-muted);
        font-size: 14px;
        background: var(--accent-bg);
        border-radius: 8px;
        position: relative;
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

        .sidebar.open {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
        }

        .dashboard-stats {
            grid-template-columns: 1fr;
        }

        .content {
            padding: 20px;
        }
    }
    </style>
</head>

<body>
    Sidebar
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

    Main Content
    <div class="main-content">
        <header class="header">
            <h1 class="page-title">Dashboard</h1>
        </header>

        <main class="content">
            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Total Users</div>
                            <div class="stat-value">{{ number_format($stats['total_users'] ?? 0) }}</div>
                            <div class="stat-change positive">
                                <span>▲</span><span>+12.5% this month</span>
                            </div>
                        </div>
                        <div class="stat-icon users">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Monthly Revenue</div>
                            <div class="stat-value">${{ number_format($stats['monthly_revenue'] ?? 0) }}</div>
                            <div class="stat-change positive">
                                <span>▲</span><span>+8.2% this month</span>
                            </div>
                        </div>
                        <div class="stat-icon revenue">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Active Conversations</div>
                            <div class="stat-value">{{ number_format($stats['active_conversations'] ?? 0) }}</div>
                            <div class="stat-change positive">
                                <span>▲</span><span>+15.3% this week</span>
                            </div>
                        </div>
                        <div class="stat-icon conversations">
                            <i class="fas fa-comments"></i>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-header">
                        <div>
                            <div class="stat-title">Messages Today</div>
                            <div class="stat-value">{{ number_format($stats['messages_today'] ?? 0) }}</div>
                            <div class="stat-change negative">
                                <span>▼</span><span>-3.1% vs yesterday</span>
                            </div>
                        </div>
                        <div class="stat-icon messages">
                            <i class="fas fa-envelope"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-grid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Usage Analytics</h3>
                        <button class="card-action">View Details</button>
                    </div>
                    <div class="card-content">
                        <div class="chart-container">
                            <canvas id="usageChart" width="400" height="200"></canvas>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Recent Activity</h3>
                        <a href="{{ route('admin.users') }}" class="card-action">View All</a>
                    </div>
                    <div class="activity-list">
                        @forelse($recent_activities ?? [] as $activity)
                        <div class="activity-item">
                            <div class="activity-avatar">
                                {{ strtoupper(substr($activity['user_name'] ?? 'U', 0, 1)) }}
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">{{ $activity['title'] ?? 'Activity' }}</div>
                                <div class="activity-description">{{ $activity['description'] ?? 'No description' }}
                                </div>
                                <div class="activity-time">{{ $activity['time'] ?? 'Just now' }}</div>
                            </div>
                        </div>
                        @empty
                        <div class="activity-item">
                            <div class="activity-avatar">N</div>
                            <div class="activity-content">
                                <div class="activity-title">No recent activity</div>
                                <div class="activity-description">Check back later for updates</div>
                                <div class="activity-time">Now</div>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Quick Actions</h3>
                </div>
                <div class="card-content">
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <a href="{{ route('admin.users') }}" class="card-action"
                            style="display: block; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; text-align: center;">
                            <i class="fas fa-user-plus"></i> Manage Users
                        </a>
                        <a href="{{ route('admin.revenue') }}" class="card-action"
                            style="display: block; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; text-align: center;">
                            <i class="fas fa-chart-bar"></i> View Revenue
                        </a>
                        <a href="{{ route('admin.conversations') }}" class="card-action"
                            style="display: block; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; text-align: center;">
                            <i class="fas fa-comments"></i> Monitor Chats
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
    // Simple chart drawing function
    function drawChart(canvasId, data) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;

        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;

        // Clear canvas
        ctx.clearRect(0, 0, width, height);

        // Sample data if none provided
        const chartData = data || [120, 150, 180, 160, 210, 240, 260, 300, 280, 320, 340, 380];

        const padding = 40;
        const chartWidth = width - padding * 2;
        const chartHeight = height - padding * 2;

        const maxValue = Math.max(...chartData);
        const minValue = Math.min(...chartData);
        const valueRange = maxValue - minValue || 1;

        // Draw grid lines
        ctx.strokeStyle = 'rgba(255,255,255,0.1)';
        ctx.lineWidth = 1;
        for (let i = 0; i <= 5; i++) {
            const y = padding + (i * chartHeight) / 5;
            ctx.beginPath();
            ctx.moveTo(padding, y);
            ctx.lineTo(width - padding, y);
            ctx.stroke();
        }

        // Draw line
        ctx.strokeStyle = '#10a37f';
        ctx.lineWidth = 3;
        ctx.beginPath();

        chartData.forEach((value, index) => {
            const x = padding + (index * chartWidth) / (chartData.length - 1);
            const y = padding + chartHeight - ((value - minValue) / valueRange) * chartHeight;

            if (index === 0) {
                ctx.moveTo(x, y);
            } else {
                ctx.lineTo(x, y);
            }
        });

        ctx.stroke();

        // Draw points
        ctx.fillStyle = '#10a37f';
        chartData.forEach((value, index) => {
            const x = padding + (index * chartWidth) / (chartData.length - 1);
            const y = padding + chartHeight - ((value - minValue) / valueRange) * chartHeight;

            ctx.beginPath();
            ctx.arc(x, y, 4, 0, Math.PI * 2);
            ctx.fill();
        });
    }

    // Initialize chart when page loads
    document.addEventListener('DOMContentLoaded', function() {
        drawChart('usageChart');
    });
    </script>
</body>

</html>