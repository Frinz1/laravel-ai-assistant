<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - {{ config('app.name') }}</title>
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

    /* Sidebar styles */
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

    /* Alert Messages */
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-success {
        background: rgba(74, 222, 128, 0.1);
        border: 1px solid var(--success-color);
        color: var(--success-color);
    }

    .alert-error {
        background: rgba(255, 107, 107, 0.1);
        border: 1px solid var(--danger-color);
        color: var(--danger-color);
    }

    .users-header {
        background: var(--secondary-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .users-header-title h1 {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .users-header-title p {
        color: var(--text-muted);
        font-size: 14px;
    }

    .users-header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .search-box {
        position: relative;
        min-width: 280px;
    }

    .search-box input {
        width: 100%;
        padding: 12px 14px 12px 40px;
        background: var(--accent-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-primary);
        outline: none;
        font-size: 14px;
    }

    .search-box .icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: var(--accent-bg);
        color: var(--text-secondary);
        cursor: pointer;
        transition: all .2s ease;
        font-weight: 600;
        text-decoration: none;
    }

    .btn:hover {
        background: var(--hover-bg);
        color: var(--text-primary);
        border-color: var(--accent-color);
    }

    .btn-primary {
        background: var(--accent-color);
        color: #fff;
        border-color: var(--accent-color);
    }

    .btn-primary:hover {
        background: #0d8f6a;
        border-color: #0d8f6a;
    }

    .users-table {
        background: var(--secondary-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .table-header {
        padding: 14px 16px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .table-title {
        font-size: 16px;
        font-weight: 700;
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
        padding: 16px;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
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

    .user-cell {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .user-avatar {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        background: var(--accent-color);
        color: #fff;
        display: grid;
        place-items: center;
        font-weight: 700;
        font-size: 12px;
    }

    .user-info h4 {
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 2px;
    }

    .user-info p {
        color: var(--text-muted);
        font-size: 12px;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .status-badge.active {
        background: rgba(74, 222, 128, .2);
        color: var(--success-color);
    }

    .status-badge.suspended {
        background: rgba(255, 107, 107, .2);
        color: var(--danger-color);
    }

    .status-badge.inactive {
        background: rgba(156, 163, 175, .2);
        color: var(--text-muted);
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border: none;
        border-radius: 6px;
        background: var(--accent-bg);
        color: var(--text-muted);
        cursor: pointer;
        transition: all .2s ease;
        display: inline-grid;
        place-items: center;
        margin: 0 2px;
    }

    .action-btn:hover {
        transform: scale(1.08);
        color: var(--text-primary);
    }

    .action-btn.edit:hover {
        background: rgba(251, 191, 36, 0.2);
        color: var(--warning-color);
    }

    .action-btn.delete:hover {
        background: rgba(255, 107, 107, 0.2);
        color: var(--danger-color);
    }

    .action-btn.view:hover {
        background: rgba(59, 130, 246, 0.2);
        color: var(--info-color);
    }

    .pagination {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 14px 16px;
        border-top: 1px solid var(--border-color);
        background: var(--accent-bg);
    }

    .pagination-info {
        color: var(--text-muted);
        font-size: 14px;
    }

    .pagination-controls {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .pagination-btn {
        width: 32px;
        height: 32px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        background: var(--secondary-bg);
        color: var(--text-muted);
        cursor: pointer;
        display: grid;
        place-items: center;
        transition: .2s;
        text-decoration: none;
    }

    .pagination-btn:hover:not(.disabled) {
        background: var(--hover-bg);
        color: var(--text-primary);
        border-color: var(--accent-color);
    }

    .pagination-btn.disabled {
        opacity: .5;
        cursor: not-allowed;
    }

    .pagination-btn.active {
        background: var(--accent-color);
        color: #fff;
        border-color: var(--accent-color);
    }

    @media (max-width: 768px) {
        .sidebar {
            transform: translateX(-100%);
        }

        .main-content {
            margin-left: 0;
        }

        .content {
            padding: 20px;
        }

        .users-header {
            flex-direction: column;
            align-items: stretch;
        }

        .users-header-actions {
            flex-direction: column;
        }

        .search-box {
            min-width: auto;
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
                    class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
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
            <h1 class="page-title">Users</h1>
        </header>

        <main class="content">
            <!-- Display Success/Error Messages -->
            @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
            </div>
            @endif

            <!-- Header -->
            <div class="users-header">
                <div class="users-header-title">
                    <h1>Users</h1>
                    <p>Manage and monitor all user accounts</p>
                </div>
                <div class="users-header-actions">
                    <form method="GET" action="{{ route('admin.users') }}" class="search-box">
                        <span class="icon"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..."
                            onchange="this.form.submit()" />
                    </form>
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add User
                    </a>
                </div>
            </div>

            <!-- Users Table -->
            <div class="users-table">
                <div class="table-header">
                    <div class="table-title">Users ({{ $users->total() }})</div>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Last Active</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($user->first_name ?? $user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div class="user-info">
                                            <h4>{{ ($user->first_name ?? '') . ' ' . ($user->last_name ?? '') ?: $user->name ?? 'Unknown User' }}
                                            </h4>
                                            <p>{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span
                                        class="status-badge {{ ($user->role ?? 'user') === 'admin' ? 'active' : 'inactive' }}">
                                        {{ ucfirst($user->role ?? 'user') }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="status-badge {{ ($user->status ?? 'active') === 'active' ? 'active' : 'suspended' }}">
                                        {{ ucfirst($user->status ?? 'active') }}
                                    </span>
                                </td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>{{ $user->updated_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="action-btn edit"
                                        title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.delete', $user) }}"
                                        style="display: inline;"
                                        onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                    @if(request('search'))
                                    No users found matching "{{ request('search') }}"
                                    @else
                                    No users found
                                    @endif
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                <div class="pagination">
                    <div class="pagination-info">
                        Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }}
                        users
                    </div>
                    <div class="pagination-controls">
                        @if ($users->onFirstPage())
                        <span class="pagination-btn disabled">‹</span>
                        @else
                        <a href="{{ $users->previousPageUrl() }}" class="pagination-btn">‹</a>
                        @endif

                        @foreach ($users->getUrlRange(1, min($users->lastPage(), 5)) as $page => $url)
                        @if ($page == $users->currentPage())
                        <span class="pagination-btn active">{{ $page }}</span>
                        @else
                        <a href="{{ $url }}" class="pagination-btn">{{ $page }}</a>
                        @endif
                        @endforeach

                        @if ($users->hasMorePages())
                        <a href="{{ $users->nextPageUrl() }}" class="pagination-btn">›</a>
                        @else
                        <span class="pagination-btn disabled">›</span>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </main>
    </div>
</body>

</html>