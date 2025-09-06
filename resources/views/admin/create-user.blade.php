<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User - {{ config('app.name') }}</title>
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

    /* Form Styles */
    .form-container {
        background: var(--secondary-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 30px;
        max-width: 600px;
        margin: 0 auto;
    }

    .form-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .form-header h1 {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .form-header p {
        color: var(--text-muted);
        font-size: 14px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: var(--text-secondary);
        margin-bottom: 8px;
    }

    .form-input,
    .form-select {
        width: 100%;
        padding: 12px 16px;
        background: var(--accent-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-primary);
        font-size: 14px;
        outline: none;
        transition: border-color 0.2s ease;
    }

    .form-input:focus,
    .form-select:focus {
        border-color: var(--accent-color);
    }

    .form-error {
        color: var(--danger-color);
        font-size: 12px;
        margin-top: 4px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        border-radius: 8px;
        border: 1px solid var(--border-color);
        background: var(--accent-bg);
        color: var(--text-secondary);
        cursor: pointer;
        transition: all .2s ease;
        font-weight: 600;
        text-decoration: none;
        font-size: 14px;
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

    .btn-secondary {
        background: var(--accent-bg);
        color: var(--text-secondary);
        border-color: var(--border-color);
    }

    .btn-secondary:hover {
        background: var(--hover-bg);
        color: var(--text-primary);
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

        .form-container {
            padding: 20px;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }

        .form-actions {
            flex-direction: column;
        }
    }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">
                <i class="fas fa-robot"></i>
            </div>
            <h2 class="sidebar-title">AI Assistant</h2>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Main</div>
                <a href="{{ route('admin.dashboard') }}" class="nav-item">
                    <span class="icon">
                        <i class="fas fa-chart-line"></i>
                    </span>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users') }}" class="nav-item active">
                    <span class="icon">
                        <i class="fas fa-users"></i>
                    </span>
                    <span>Users</span>
                </a>
                <a href="{{ route('admin.conversations') }}" class="nav-item">
                    <span class="icon">
                        <i class="fas fa-comments"></i>
                    </span>
                    <span>Conversations</span>
                </a>
                <a href="{{ route('admin.revenue') }}" class="nav-item">
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
            <h1 class="page-title">Create User</h1>
            <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </header>

        <main class="content">
            @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin-top: 8px; margin-left: 20px;">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <div class="form-container">
                <div class="form-header">
                    <h1>Create New User</h1>
                    <p>Add a new user to the system</p>
                </div>

                <form method="POST" action="{{ route('admin.users.store') }}">
                    @csrf

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input type="text" id="first_name" name="first_name" class="form-input"
                                value="{{ old('first_name') }}" required>
                            @error('first_name')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input type="text" id="last_name" name="last_name" class="form-input"
                                value="{{ old('last_name') }}" required>
                            @error('last_name')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" id="email" name="email" class="form-input" value="{{ old('email') }}"
                            required>
                        @error('email')
                        <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" id="password" name="password" class="form-input" required>
                            @error('password')
                            <div class="form-error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="password_confirmation" class="form-label">Confirm Password *</label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                class="form-input" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="role" class="form-label">Role *</label>
                        <select id="role" name="role" class="form-select" required>
                            <option value="">Select Role</option>
                            <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')
                        <div class="form-error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-actions">
                        <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create User
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>

</html>