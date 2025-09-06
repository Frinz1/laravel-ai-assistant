<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - {{ config('app.name') }}</title>
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

    .header-left {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .back-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 6px;
        background: var(--accent-bg);
        color: var(--text-secondary);
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid var(--border-color);
    }

    .back-btn:hover {
        background: var(--hover-bg);
        color: var(--text-primary);
        border-color: var(--accent-color);
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

    /* Form Container */
    .form-container {
        background: var(--secondary-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .form-header {
        padding: 20px;
        border-bottom: 1px solid var(--border-color);
        background: var(--accent-bg);
    }

    .form-header h2 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .form-header p {
        color: var(--text-muted);
        font-size: 14px;
    }

    .form-content {
        padding: 24px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 24px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-secondary);
    }

    .form-label.required::after {
        content: ' *';
        color: var(--danger-color);
    }

    .form-input,
    .form-select {
        width: 100%;
        padding: 12px 14px;
        background: var(--accent-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        color: var(--text-primary);
        outline: none;
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .form-input:focus,
    .form-select:focus {
        border-color: var(--accent-color);
        background: var(--primary-bg);
    }

    .form-input::placeholder {
        color: var(--text-muted);
    }

    .form-select {
        cursor: pointer;
    }

    .form-select option {
        background: var(--secondary-bg);
        color: var(--text-primary);
    }

    .error-message {
        font-size: 12px;
        color: var(--danger-color);
        margin-top: 4px;
    }

    .form-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding-top: 20px;
        border-top: 1px solid var(--border-color);
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 16px;
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

    .btn-danger {
        background: var(--danger-color);
        color: #fff;
        border-color: var(--danger-color);
    }

    .btn-danger:hover {
        background: #ff5252;
        border-color: #ff5252;
    }

    .user-avatar-large {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: var(--accent-color);
        display: grid;
        place-items: center;
        color: white;
        font-size: 28px;
        font-weight: 700;
        margin: 0 auto 16px;
    }

    .user-preview {
        text-align: center;
        padding: 20px;
        background: var(--accent-bg);
        border-radius: 8px;
        margin-bottom: 24px;
    }

    .user-preview h3 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .user-preview p {
        color: var(--text-muted);
        font-size: 14px;
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

        .form-grid {
            grid-template-columns: 1fr;
        }

        .header {
            padding: 0 20px;
        }

        .form-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .form-actions .btn {
            justify-content: center;
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
            <div class="header-left">
                <a href="{{ route('admin.users') }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                    Back to Users
                </a>
                <h1 class="page-title">Edit User</h1>
            </div>
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

            <!-- User Preview -->
            <div class="user-preview">
                <div class="user-avatar-large">
                    {{ strtoupper(substr($user->first_name ?? $user->name ?? 'U', 0, 1)) }}
                </div>
                <h3>{{ ($user->first_name ?? '') . ' ' . ($user->last_name ?? '') ?: $user->name ?? 'Unknown User' }}
                </h3>
                <p>{{ $user->email }}</p>
            </div>

            <!-- Edit Form -->
            <div class="form-container">
                <div class="form-header">
                    <h2>Edit User Information</h2>
                    <p>Update user details and permissions</p>
                </div>

                <div class="form-content">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="first_name" class="form-label required">First Name</label>
                                <input type="text" id="first_name" name="first_name"
                                    class="form-input @error('first_name') error @enderror"
                                    value="{{ old('first_name', $user->first_name) }}" placeholder="Enter first name"
                                    required>
                                @error('first_name')
                                <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="last_name" class="form-label required">Last Name</label>
                                <input type="text" id="last_name" name="last_name"
                                    class="form-input @error('last_name') error @enderror"
                                    value="{{ old('last_name', $user->last_name) }}" placeholder="Enter last name"
                                    required>
                                @error('last_name')
                                <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group full-width">
                                <label for="email" class="form-label required">Email Address</label>
                                <input type="email" id="email" name="email"
                                    class="form-input @error('email') error @enderror"
                                    value="{{ old('email', $user->email) }}" placeholder="Enter email address" required>
                                @error('email')
                                <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="role" class="form-label required">Role</label>
                                <select id="role" name="role" class="form-select @error('role') error @enderror"
                                    required>
                                    <option value="user"
                                        {{ old('role', $user->role ?? 'user') === 'user' ? 'selected' : '' }}>User
                                    </option>
                                    <option value="admin"
                                        {{ old('role', $user->role ?? 'user') === 'admin' ? 'selected' : '' }}>Admin
                                    </option>
                                </select>
                                @error('role')
                                <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="status" class="form-label required">Status</label>
                                <select id="status" name="status" class="form-select @error('status') error @enderror"
                                    required>
                                    <option value="active"
                                        {{ old('status', $user->status ?? 'active') === 'active' ? 'selected' : '' }}>
                                        Active</option>
                                    <option value="suspended"
                                        {{ old('status', $user->status ?? 'active') === 'suspended' ? 'selected' : '' }}>
                                        Suspended</option>
                                    <option value="inactive"
                                        {{ old('status', $user->status ?? 'active') === 'inactive' ? 'selected' : '' }}>
                                        Inactive</option>
                                </select>
                                @error('status')
                                <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group full-width">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" id="password" name="password"
                                    class="form-input @error('password') error @enderror"
                                    placeholder="Leave blank to keep current password">
                                @error('password')
                                <div class="error-message">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group full-width">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation"
                                    class="form-input" placeholder="Confirm new password">
                            </div>
                        </div>

                        <div class="form-actions">
                            <div>
                                @if($user->id !== auth()->id())
                                <button type="button" class="btn btn-danger"
                                    onclick="if(confirm('Are you sure you want to delete this user? This action cannot be undone.')) { document.getElementById('delete-form').submit(); }">
                                    <i class="fas fa-trash"></i>
                                    Delete User
                                </button>
                                @endif
                            </div>
                            <div style="display: flex; gap: 12px;">
                                <a href="{{ route('admin.users') }}" class="btn">
                                    <i class="fas fa-times"></i>
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i>
                                    Update User
                                </button>
                            </div>
                        </div>
                    </form>

                    @if($user->id !== auth()->id())
                    <!-- Hidden Delete Form -->
                    <form id="delete-form" method="POST" action="{{ route('admin.users.delete', $user) }}"
                        style="display: none;">
                        @csrf
                        @method('DELETE')
                    </form>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <script>
    // Auto-submit search form on input change
    document.addEventListener('DOMContentLoaded', function() {
        // Password confirmation validation
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');

        function validatePasswords() {
            if (password.value && passwordConfirmation.value) {
                if (password.value !== passwordConfirmation.value) {
                    passwordConfirmation.setCustomValidity('Passwords do not match');
                } else {
                    passwordConfirmation.setCustomValidity('');
                }
            } else {
                passwordConfirmation.setCustomValidity('');
            }
        }

        password.addEventListener('input', validatePasswords);
        passwordConfirmation.addEventListener('input', validatePasswords);

        // Form validation
        const form = document.querySelector('form[method="POST"]');
        form.addEventListener('submit', function(e) {
            validatePasswords();
            if (!passwordConfirmation.checkValidity()) {
                e.preventDefault();
                passwordConfirmation.focus();
            }
        });
    });
    </script>
</body>

</html>