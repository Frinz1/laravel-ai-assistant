<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Chat - {{ config('app.name') }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    :root {
        --primary-bg: #0a0a0a;
        --secondary-bg: #1a1a1a;
        --accent-bg: #2a2a2a;
        --border-color: #404040;
        --text-primary: #ffffff;
        --text-secondary: #b0b0b0;
        --text-muted: #808080;
        --accent-color: #00d4aa;
        --user-color: #00d4aa;
        --ai-color: #8b5cf6;
        --hover-bg: #333333;
        --message-bg-user: rgba(0, 212, 170, 0.1);
        --message-bg-ai: rgba(139, 92, 246, 0.1);
        --sidebar-w: 260px;
        --composer-h: 92px;
        --typing-gap: 12px;
    }

    body {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
        background: var(--primary-bg);
        color: var(--text-primary);
        height: 100vh;
        overflow: hidden;
    }

    .chat-container {
        display: flex;
        height: 100vh;
        position: relative;
    }

    .sidebar {
        width: var(--sidebar-w);
        background: var(--secondary-bg);
        border-right: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        transition: transform .3s ease;
    }

    .sidebar-header {
        padding: 20px;
        border-bottom: 1px solid var(--border-color);
    }

    .new-chat-btn {
        width: 100%;
        padding: 12px 16px;
        background: var(--accent-color);
        border: none;
        color: white;
        border-radius: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all .2s ease;
        font-size: 14px;
        font-weight: 500;
    }

    .new-chat-btn:hover {
        background: #00b894;
        transform: translateY(-1px);
    }

    .chat-history {
        flex: 1;
        overflow-y: auto;
        padding: 8px;
    }

    .chat-item {
        padding: 12px;
        margin: 4px 0;
        border-radius: 8px;
        cursor: pointer;
        transition: all .2s ease;
        font-size: 14px;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid transparent;
    }

    .chat-item:hover {
        background: var(--accent-bg);
        border-color: var(--border-color);
    }

    .chat-item.active {
        background: rgba(0, 212, 170, 0.15);
        color: var(--text-primary);
        border-color: var(--accent-color);
    }

    .chat-title {
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .chat-delete {
        border: none;
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-muted);
        border-radius: 6px;
        padding: 6px 8px;
        font-size: 12px;
        cursor: pointer;
        opacity: 0;
        transition: all .2s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .chat-item:hover .chat-delete {
        opacity: 1;
    }

    .chat-delete .spinner {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid var(--text-muted);
        border-top-color: transparent;
        display: none;
        animation: spin .8s linear infinite;
    }

    .chat-delete.loading {
        pointer-events: none;
        opacity: .6;
    }

    .chat-delete.loading i {
        display: none;
    }

    .chat-delete.loading .spinner {
        display: inline-block;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .sidebar-footer {
        padding: 20px;
        border-top: 1px solid var(--border-color);
        background: var(--secondary-bg);
    }

    .user-profile {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        border-radius: 8px;
        color: var(--text-secondary);
        margin-bottom: 16px;
        background: rgba(255, 255, 255, 0.02);
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--accent-color), var(--ai-color));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: #fff;
        font-weight: 600;
    }

    .user-info {
        flex: 1;
        min-width: 0;
    }

    .user-name {
        font-size: 14px;
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 2px;
    }

    .user-plan {
        font-size: 12px;
        color: var(--text-muted);
    }

    .sidebar-actions {
        display: flex;
        gap: 8px;
    }

    .action-btn {
        flex: 1;
        padding: 10px 12px;
        background: var(--accent-bg);
        border: 1px solid var(--border-color);
        color: var(--text-secondary);
        border-radius: 6px;
        cursor: pointer;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all .2s ease;
        text-decoration: none;
    }

    .action-btn:hover {
        background: var(--hover-bg);
        color: var(--text-primary);
        transform: translateY(-1px);
    }

    .main-content {
        flex: 1;
        display: flex;
        flex-direction: column;
        min-height: 0;
        position: relative;
    }

    .chat-interface {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        scroll-behavior: smooth;
        padding-bottom: calc(var(--composer-h) + var(--typing-gap) + 24px);
    }

    /* Messages */
    .message {
        margin-bottom: 16px;
        display: flex;
        gap: 12px;
        animation: fadeInUp .3s ease;
    }

    .message-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 14px;
        color: #fff;
        font-weight: 500;
    }

    .message-content {
        flex: 1;
        padding: 0;
        max-width: calc(100% - 52px);
    }

    .message-text ul,
    .message-text ol {
        margin: 8px 0 8px 0;
        /* vertical spacing only */
        padding-left: 20px;
        /* indent neatly inside bubble */
    }

    .message-text li {
        margin: 4px 0;
        line-height: 1.6;
    }

    .message-text a {
        color: var(--accent-color);
        /* use your theme’s accent color */
        text-decoration: underline;
        word-break: break-word;
        /* prevents overflow */
        transition: color 0.2s ease;
    }

    .message-text a:hover {
        color: #00b894;
        /* brighter hover */
        text-decoration: none;
    }

    .message-text {
        display: inline-block;
        line-height: 1.6;
        color: var(--text-primary);
        word-wrap: break-word;
        padding: 12px 16px;
        border-radius: 12px;
        margin: 0;
        background: var(--accent-bg);
        border: 1px solid var(--border-color);
        position: relative;
        width: auto;
        max-width: 70%;
        /* Desktop width limit */
    }

    /* User LEFT */
    .user-message {
        flex-direction: row;
        justify-content: flex-start;
    }

    .user-message .message-avatar {
        background: linear-gradient(135deg, var(--user-color), #00b894);
    }

    .user-message .message-content {
        display: flex;
        justify-content: flex-start;
    }

    .user-message .message-text {
        background: var(--message-bg-user);
        border-color: rgba(0, 212, 170, 0.3);
        border-radius: 12px 12px 12px 4px;
        align-self: flex-start;
    }

    /* AI RIGHT */
    .ai-message {
        flex-direction: row-reverse;
        justify-content: flex-start;
    }

    .ai-message .message-avatar {
        background: linear-gradient(135deg, var(--ai-color), #7c3aed);
    }

    .ai-message .message-content {
        display: flex;
        justify-content: flex-end;
    }

    .ai-message .message-text {
        background: var(--message-bg-ai);
        border-color: rgba(139, 92, 246, 0.3);
        border-radius: 12px 12px 4px 12px;
        align-self: flex-start;
        max-width: 80%;
        /* AI can use wider bubbles */
    }

    .input-container {
        padding: 20px 18px;
        border-top: 1px solid var(--border-color);
        background: var(--primary-bg);
        position: sticky;
        bottom: 0;
        z-index: 4;
        height: var(--composer-h);
        display: flex;
        align-items: center;
    }

    .input-wrapper {
        position: relative;
        max-width: 800px;
        margin: 0 auto;
        display: flex;
        align-items: flex-end;
        width: 100%;
    }

    .message-input {
        width: 100%;
        min-height: 52px;
        max-height: 120px;
        padding: 16px 52px 16px 20px;
        background: var(--accent-bg);
        border: 2px solid var(--border-color);
        border-radius: 16px;
        color: var(--text-primary);
        font-size: 15px;
        resize: none;
        outline: none;
        transition: all .3s ease;
        line-height: 1.5;
        overflow-y: auto;
        font-family: inherit;
    }

    .message-input:focus {
        border-color: var(--accent-color);
        background: rgba(42, 42, 42, 0.8);
        box-shadow: 0 0 0 3px rgba(0, 212, 170, 0.1);
    }

    .message-input::placeholder {
        color: var(--text-muted);
    }

    .send-btn {
        position: absolute;
        right: 8px;
        bottom: 8px;
        width: 36px;
        height: 36px;
        background: var(--accent-color);
        border: none;
        border-radius: 12px;
        color: #fff;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all .2s ease;
        opacity: .6;
    }

    .send-btn:hover:not(:disabled) {
        background: #00b894;
        opacity: 1;
        transform: translateY(-1px) scale(1.05);
    }

    .send-btn:disabled {
        opacity: .3;
        cursor: not-allowed;
    }

    .send-btn.active {
        opacity: 1;
        background: var(--accent-color);
    }

    .welcome-screen {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        text-align: center;
        padding: 40px;
    }

    .logo {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--accent-color), var(--ai-color));
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
        font-size: 32px;
        color: #fff;
        box-shadow: 0 8px 24px rgba(0, 212, 170, 0.3);
    }

    .welcome-title {
        font-size: 28px;
        font-weight: 600;
        margin-bottom: 12px;
        background: linear-gradient(135deg, var(--text-primary), var(--text-secondary));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .welcome-subtitle {
        color: var(--text-muted);
        font-size: 16px;
        margin-bottom: 32px;
        max-width: 400px;
        line-height: 1.5;
    }

    .typing-indicator {
        display: none;
        position: sticky;
        bottom: calc(var(--composer-h) + var(--typing-gap));
        z-index: 4;
        margin: 0 auto;
        width: fit-content;
        background: rgba(139, 92, 246, 0.1);
        border: 1px solid rgba(139, 92, 246, 0.3);
        padding: 20px 18px;
        border-radius: 20px;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    }

    .typing-indicator .message {
        margin: 0;
        gap: 12px;
    }

    .typing-indicator .message-avatar {
        width: 24px;
        height: 24px;
        font-size: 12px;
    }

    .typing-indicator .message-content {
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--ai-color);
        font-style: italic;
        font-size: 14px;
    }

    .typing-dots {
        display: flex;
        gap: 4px;
    }

    .typing-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--ai-color);
        animation: typing 1.4s infinite ease-in-out;
    }

    .typing-dot:nth-child(1) {
        animation-delay: -0.32s;
    }

    .typing-dot:nth-child(2) {
        animation-delay: -0.16s;
    }

    .typing-dot:nth-child(3) {
        animation-delay: 0s;
    }

    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.7);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(4px);
    }

    .modal {
        width: min(420px, calc(100% - 32px));
        background: var(--secondary-bg);
        color: var(--text-primary);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    }

    .modal h3 {
        margin-bottom: 12px;
        font-size: 18px;
        font-weight: 600;
    }

    .modal p {
        color: var(--text-secondary);
        margin-bottom: 20px;
        line-height: 1.5;
    }

    .modal .actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
    }

    .btn {
        border: 1px solid var(--border-color);
        background: var(--accent-bg);
        color: var(--text-primary);
        padding: 10px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all .2s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .btn-danger {
        background: rgba(239, 68, 68, 0.15);
        color: #ef4444;
        border-color: rgba(239, 68, 68, 0.4);
    }

    .btn-danger:hover {
        background: rgba(239, 68, 68, 0.25);
    }

    .btn-primary {
        background: var(--accent-color);
        color: white;
        border-color: var(--accent-color);
    }

    .btn-primary:hover {
        background: #00b894;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes typing {

        0%,
        80%,
        100% {
            transform: scale(0);
        }

        40% {
            transform: scale(1);
        }
    }

    @media(max-width:768px) {
        .sidebar {
            transform: translateX(-100%);
            position: absolute;
            height: 100%;
            z-index: 10;
            width: 85vw;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .chat-messages {
            padding: 16px;
        }

        .input-container {
            padding: 16px;
        }

        .message {
            gap: 10px;
        }

        .message-avatar {
            width: 28px;
            height: 28px;
            font-size: 13px;
        }

        .message-text {
            max-width: 90%;
            font-size: 14px;
        }
    }

    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        background: transparent;
    }

    ::-webkit-scrollbar-thumb {
        background: var(--border-color);
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: var(--text-muted);
    }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
</head>

<body>
    <div class="chat-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <button class="new-chat-btn" id="newChatBtn"><i class="fas fa-plus"></i> New chat</button>
            </div>
            <div class="chat-history" id="chatHistory">
                @foreach($conversations as $conversation)
                <div class="chat-item" data-conversation-id="{{ $conversation->id }}">
                    <span class="chat-title">{{ Str::limit($conversation->title,40) }}</span>
                    <button class="chat-delete" title="Delete" data-conversation-id="{{ $conversation->id }}">
                        <span class="spinner"></span>
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                @endforeach
            </div>
            <div class="sidebar-footer">
                <div class="user-profile">
                    <div class="user-avatar">{{ strtoupper(substr(auth()->user()->first_name,0,1)) }}</div>
                    <div class="user-info">
                        <div class="user-name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                        <div class="user-plan">{{ ucfirst(auth()->user()->current_plan ?? 'free') }} Plan</div>
                    </div>
                </div>
                <div class="sidebar-actions">
                    <a href="{{ route('plans.index') }}" class="action-btn"><i class="fas fa-crown"></i> Plans</a>
                    <form method="POST" action="{{ route('logout') }}" style="flex:1;">@csrf
                        <button type="submit" class="action-btn" style="width:100%;"><i class="fas fa-sign-out-alt"></i>
                            Logout</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Main -->
        <div class="main-content">
            <div class="chat-interface">
                <div class="chat-messages" id="chatMessages">
                    <div class="welcome-screen" id="welcomeScreen">
                        <div class="logo"><i class="fas fa-robot"></i></div>
                        <h1 class="welcome-title">How can I help you today?</h1>
                        <p class="welcome-subtitle">Ask me anything, and I'll do my best to help you.</p>
                    </div>
                </div>
                <!-- Typing Indicator -->
                <div class="typing-indicator" id="typingIndicator">
                    <div class="message">
                        <div class="message-avatar ai-message"><i class="fas fa-robot"></i></div>
                        <div class="message-content">
                            <span>AI is typing</span>
                            <div class="typing-dots">
                                <div class="typing-dot"></div>
                                <div class="typing-dot"></div>
                                <div class="typing-dot"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Input -->
            <div class="input-container">
                <div class="input-wrapper">
                    <textarea id="messageInput" class="message-input" placeholder="Message AI assistant..."
                        rows="1"></textarea>
                    <button id="sendBtn" class="send-btn" disabled><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirm Modal -->
    <div id="confirmBackdrop" class="modal-backdrop">
        <div class="modal">
            <h3>Delete conversation?</h3>
            <p>This action cannot be undone.</p>
            <div class="actions">
                <button class="btn btn-primary" id="confirmCancel">Cancel</button>
                <button class="btn btn-danger" id="confirmOk">Delete</button>
            </div>
        </div>
    </div>

    <script>
    class ChatInterface {
        constructor() {
            this.currentConversationId = null;
            this.messageInput = document.getElementById('messageInput');
            this.sendBtn = document.getElementById('sendBtn');
            this.chatMessages = document.getElementById('chatMessages');
            this.welcomeScreen = document.getElementById('welcomeScreen');
            this.typingIndicator = document.getElementById('typingIndicator');
            this.newChatBtn = document.getElementById('newChatBtn');
            this.chatHistory = document.getElementById('chatHistory');
            this.confirmBackdrop = document.getElementById('confirmBackdrop');
            this.confirmOk = document.getElementById('confirmOk');
            this.confirmCancel = document.getElementById('confirmCancel');
            this.initEventListeners();
            this.autoResizeTextarea();
        }
        initEventListeners() {
            this.sendBtn.addEventListener('click', () => this.sendMessage());
            this.messageInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter' && !e.shiftKey) {
                    e.preventDefault();
                    this.sendMessage();
                }
            });
            this.messageInput.addEventListener('input', () => {
                this.toggleSendButton();
                this.autoResizeTextarea();
            });
            this.newChatBtn.addEventListener('click', () => this.startNewChat());
            document.querySelectorAll('.chat-item').forEach(item => {
                item.addEventListener('click', () => {
                    const id = item.dataset.conversationId;
                    this.loadConversation(id);
                });
            });
            this.chatHistory.addEventListener('click', (e) => {
                const btn = e.target.closest('.chat-delete');
                if (!btn) return;
                e.stopPropagation();
                const convId = btn.getAttribute('data-conversation-id');
                if (!convId) return;
                this.confirmDialog().then(ok => {
                    if (!ok) return;
                    btn.classList.add('loading');
                    this.deleteConversation(convId).finally(() => btn.classList.remove('loading'));
                });
            });
        }
        toggleSendButton() {
            const hasText = this.messageInput.value.trim().length > 0;
            this.sendBtn.disabled = !hasText;
            this.sendBtn.classList.toggle('active', hasText);
        }
        autoResizeTextarea() {
            this.messageInput.style.height = 'auto';
            const h = this.messageInput.scrollHeight;
            this.messageInput.style.height = Math.min(h, 120) + 'px';
        }
        md(html) {
            try {
                return marked.parse(html);
            } catch {
                return html.replace(/\n/g, '<br>');
            }
        }
        addMessage(content, type) {
            const el = document.createElement('div');
            el.className = `message ${type}-message`;
            el.innerHTML = `
            <div class="message-avatar"><i class="fas ${type==='user'?'fa-user':'fa-robot'}"></i></div>
            <div class="message-content"><div class="message-text">${this.md(content)}</div></div>`;
            this.chatMessages.appendChild(el);
            this.scrollToBottom();
        }
        showTyping() {
            this.typingIndicator.style.display = 'block';
            this.scrollToBottom();
        }
        hideTyping() {
            this.typingIndicator.style.display = 'none';
        }
        scrollToBottom() {
            setTimeout(() => {
                this.chatMessages.scrollTop = this.chatMessages.scrollHeight;
            }, 50);
        }
        startNewChat() {
            this.currentConversationId = null;
            this.chatMessages.innerHTML = `
            <div class="welcome-screen" id="welcomeScreen">
                <div class="logo"><i class="fas fa-robot"></i></div>
                <h1 class="welcome-title">How can I help you today?</h1>
                <p class="welcome-subtitle">Ask me anything, and I'll do my best to help you.</p>
            </div>`;
            document.querySelectorAll('.chat-item').forEach(i => i.classList.remove('active'));
            this.welcomeScreen = document.getElementById('welcomeScreen');
        }
        async loadConversation(id) {
            document.querySelectorAll('.chat-item').forEach(i => i.classList.remove('active'));
            const active = document.querySelector(`[data-conversation-id="${id}"]`);
            if (active) active.classList.add('active');
            const res = await fetch(`/api/chat/conversation/${id}`);
            if (!res.ok) return;
            const data = await res.json();
            if (!data.success) return;
            this.currentConversationId = id;
            this.chatMessages.innerHTML = '';
            (data.messages || []).forEach(m => this.addMessage(m.content, m.is_user ? 'user' : 'ai'));
        }
        addHistoryItem(id, title) {
            const existing = this.chatHistory.querySelector(`[data-conversation-id="${id}"]`);
            if (existing) {
                const span = document.createElement('span');
                span.className = 'chat-title';
                span.textContent = title;
                const old = existing.querySelector('.chat-title');
                if (old) old.replaceWith(span);
                else existing.prepend(span);
                existing.classList.add('active');
                this.chatHistory.prepend(existing);
                return;
            }
            const el = document.createElement('div');
            el.className = 'chat-item active';
            el.dataset.conversationId = id;
            el.innerHTML = `
            <span class="chat-title"></span>
            <button class="chat-delete" title="Delete" data-conversation-id="${id}">
                <span class="spinner"></span><i class="fas fa-trash"></i>
            </button>`;
            el.querySelector('.chat-title').textContent = title;
            el.addEventListener('click', () => this.loadConversation(id));
            document.querySelectorAll('.chat-item').forEach(i => i.classList.remove('active'));
            this.chatHistory.prepend(el);
        }
        async deleteConversation(id) {
            try {
                const res = await fetch(`/api/chat/conversation/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                if (!res.ok) {
                    const data = await res.json().catch(() => ({}));
                    alert(data?.error || 'Failed to delete.');
                    return;
                }
                const item = this.chatHistory.querySelector(`[data-conversation-id="${id}"]`);
                if (item) item.remove();
                if (String(this.currentConversationId) === String(id)) {
                    this.currentConversationId = null;
                    this.startNewChat();
                }
            } catch (e) {
                console.error('Delete error:', e);
                alert('An error occurred.');
            }
        }
        async sendMessage() {
            const message = this.messageInput.value.trim();
            if (!message) return;
            if (this.welcomeScreen) this.welcomeScreen.style.display = 'none';
            this.addMessage(message, 'user');
            this.messageInput.value = '';
            this.toggleSendButton();
            this.autoResizeTextarea();
            this.showTyping();
            try {
                const res = await fetch('/api/chat/send', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        message,
                        conversation_id: this.currentConversationId
                    })
                });
                const data = await res.json();
                if (res.ok && data.success) {
                    const wasNew = !this.currentConversationId;
                    this.currentConversationId = data.conversation_id;
                    const aiText = (data.assistant_message && data.assistant_message.content) || data.response ||
                        data.text || '...';
                    this.hideTyping();
                    this.addMessage(aiText, 'ai');
                    if (wasNew) {
                        const convRes = await fetch(`/api/chat/conversation/${this.currentConversationId}`);
                        if (convRes.ok) {
                            const convData = await convRes.json();
                            const title = (convData && convData.title) ? convData.title : (message.length > 40 ?
                                message.slice(0, 40) + '...' : message);
                            this.addHistoryItem(this.currentConversationId, title);
                        } else {
                            const title = (message.length > 40 ? message.slice(0, 40) + '...' : message);
                            this.addHistoryItem(this.currentConversationId, title);
                        }
                    } else {
                        const node = this.chatHistory.querySelector(
                            `[data-conversation-id="${this.currentConversationId}"]`);
                        if (node) {
                            this.chatHistory.prepend(node);
                            node.classList.add('active');
                        }
                    }
                } else {
                    this.hideTyping();
                    const errText = (data && data.error) ? data.error : 'Sorry, I encountered an error.';
                    this.addMessage(errText, 'ai');
                }
            } catch (e) {
                console.error('Error sending:', e);
                this.hideTyping();
                this.addMessage('Sorry, I encountered an error.', 'ai');
            }
        }
        confirmDialog() {
            return new Promise((resolve) => {
                const show = () => this.confirmBackdrop.style.display = 'flex';
                const hide = () => this.confirmBackdrop.style.display = 'none';
                const onOk = () => {
                    cleanup();
                    resolve(true);
                }
                const onCancel = () => {
                    cleanup();
                    resolve(false);
                }
                const onKey = (e) => {
                    if (e.key === 'Escape') onCancel();
                }
                const cleanup = () => {
                    hide();
                    this.confirmOk.removeEventListener('click', onOk);
                    this.confirmCancel.removeEventListener('click', onCancel);
                    document.removeEventListener('keydown', onKey);
                }
                this.confirmOk.addEventListener('click', onOk);
                this.confirmCancel.addEventListener('click', onCancel);
                document.addEventListener('keydown', onKey);
                show();
            });
        }
    }
    document.addEventListener('DOMContentLoaded', () => new ChatInterface());
    </script>
</body>

</html>