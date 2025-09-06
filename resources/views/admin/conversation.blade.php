<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conversations Management - {{ config('app.name') }}</title>
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
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: var(--primary-bg);
        color: var(--text-primary);
        min-height: 100vh;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 700;
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

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: var(--secondary-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        text-align: center;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: var(--accent-color);
        margin-bottom: 8px;
    }

    .stat-label {
        color: var(--text-secondary);
        font-size: 14px;
    }

    .conversations-table {
        background: var(--secondary-bg);
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
    }

    .table-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-title {
        font-size: 1.25rem;
        font-weight: 600;
    }

    .filters {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .filter-select {
        background: var(--accent-bg);
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-primary);
        padding: 8px 12px;
        font-size: 14px;
    }

    .search-box {
        display: flex;
        align-items: center;
        gap: 12px;
        background: var(--accent-bg);
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 8px 12px;
    }

    .search-input {
        background: none;
        border: none;
        color: var(--text-primary);
        outline: none;
        width: 200px;
    }

    .search-input::placeholder {
        color: var(--text-muted);
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

    .conversation-info {
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .conversation-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: var(--info-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
        flex-shrink: 0;
    }

    .conversation-details h4 {
        font-weight: 600;
        margin-bottom: 4px;
        max-width: 300px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .conversation-details p {
        color: var(--text-muted);
        font-size: 12px;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--accent-color);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        font-size: 12px;
    }

    .message-count {
        background: var(--accent-color);
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge.active {
        background: rgba(74, 222, 128, 0.2);
        color: var(--success-color);
    }

    .status-badge.inactive {
        background: rgba(156, 163, 175, 0.2);
        color: #9ca3af;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
    }

    .action-btn {
        padding: 6px 12px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        background: none;
        color: var(--text-secondary);
        cursor: pointer;
        font-size: 12px;
        transition: all 0.2s ease;
    }

    .action-btn:hover {
        background: var(--hover-bg);
        color: var(--text-primary);
    }

    .action-btn.danger:hover {
        background: rgba(255, 107, 107, 0.1);
        color: var(--danger-color);
        border-color: var(--danger-color);
    }

    .pagination {
        padding: 20px 24px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 12px;
    }

    .pagination a,
    .pagination span {
        padding: 8px 12px;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        color: var(--text-secondary);
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .pagination a:hover {
        background: var(--hover-bg);
        color: var(--text-primary);
    }

    .pagination .current {
        background: var(--accent-color);
        color: white;
        border-color: var(--accent-color);
    }

    @media (max-width: 768px) {
        .container {
            padding: 20px 10px;
        }

        .header {
            flex-direction: column;
            gap: 20px;
            text-align: center;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .table-header {
            flex-direction: column;
            gap: 16px;
        }

        .filters {
            flex-direction: column;
            width: 100%;
        }

        .search-input {
            width: 100%;
        }

        th,
        td {
            padding: 12px 16px;
        }

        .conversation-details h4 {
            max-width: 200px;
        }

        .action-buttons {
            flex-direction: column;
        }
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1 class="page-title">Conversations Management</h1>
            <a href="{{ route('admin.dashboard') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Back to Dashboard
            </a>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">{{ $conversations->total() }}</div>
                <div class="stat-label">Total Conversations</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $conversations->where('is_active', true)->count() }}</div>
                <div class="stat-label">Active Conversations</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">{{ $conversations->sum(function($conv) { return $conv->messages->count(); }) }}
                </div>
                <div class="stat-label">Total Messages</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">
                    {{ number_format($conversations->avg(function($conv) { return $conv->messages->count(); }), 1) }}
                </div>
                <div class="stat-label">Avg Messages/Conv</div>
            </div>
        </div>

        <div class="conversations-table">
            <div class="table-header">
                <h2 class="table-title">All Conversations</h2>
                <div class="filters">
                    <select class="filter-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <div class="search-box">
                        <i class="fas fa-search" style="color: var(--text-muted);"></i>
                        <input type="text" class="search-input" placeholder="Search conversations..." id="searchInput">
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Conversation</th>
                            <th>User</th>
                            <th>Messages</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Last Activity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($conversations as $conversation)
                        <tr>
                            <td>
                                <div class="conversation-info">
                                    <div class="conversation-icon">
                                        <i class="fas fa-comments"></i>
                                    </div>
                                    <div class="conversation-details">
                                        <h4>{{ $conversation->title }}</h4>
                                        <p>ID: {{ $conversation->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($conversation->user->first_name, 0, 1)) }}{{ strtoupper(substr($conversation->user->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; font-size: 14px;">
                                            {{ $conversation->user->full_name }}</div>
                                        <div style="color: var(--text-muted); font-size: 12px;">
                                            {{ $conversation->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="message-count">{{ $conversation->messages->count() }}</span>
                            </td>
                            <td>
                                <span class="status-badge {{ $conversation->is_active ? 'active' : 'inactive' }}">
                                    {{ $conversation->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>{{ $conversation->created_at->format('M d, Y') }}</td>
                            <td>{{ $conversation->updated_at->diffForHumans() }}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn" onclick="viewConversation({{ $conversation->id }})">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <button class="action-btn" onclick="exportConversation({{ $conversation->id }})">
                                        <i class="fas fa-download"></i> Export
                                    </button>
                                    <button class="action-btn danger"
                                        onclick="deleteConversation({{ $conversation->id }})">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 40px;">
                                No conversations found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($conversations->hasPages())
            <div class="pagination">
                {{ $conversations->links() }}
            </div>
            @endif
        </div>
    </div>

    <script>
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // Status filter
    document.getElementById('statusFilter').addEventListener('change', function(e) {
        const filterValue = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            if (!filterValue) {
                row.style.display = '';
            } else {
                const statusBadge = row.querySelector('.status-badge');
                const status = statusBadge ? statusBadge.textContent.toLowerCase() : '';
                row.style.display = status.includes(filterValue) ? '' : 'none';
            }
        });
    });

    // Conversation actions
    function viewConversation(conversationId) {
        // Implement view conversation functionality
        alert('View conversation functionality would be implemented here');
    }

    function exportConversation(conversationId) {
        // Implement export conversation functionality
        alert('Export conversation functionality would be implemented here');
    }

    function deleteConversation(conversationId) {
        if (confirm('Are you sure you want to delete this conversation? This action cannot be undone.')) {
            // Implement delete conversation functionality
            alert('Delete conversation functionality would be implemented here');
        }
    }
    </script>
</body>

</html>