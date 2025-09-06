<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - {{ config('app.name') }}</title>
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
            --hover-bg: #343541;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--primary-bg) 0%, #1a1a3e 100%);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }

        .register-container {
            background: var(--secondary-bg);
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 480px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border-color);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-section {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            font-size: 24px;
            color: white;
        }

        .logo-title {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
            background: linear-gradient(135deg, var(--text-primary) 0%, var(--accent-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .logo-subtitle {
            color: var(--text-muted);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-label {
            display: block;
            color: var(--text-primary);
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .form-input {
            width: 100%;
            padding: 12px 16px;
            background: var(--accent-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-primary);
            font-size: 16px;
            transition: all 0.2s ease;
        }

        .form-input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(16, 163, 127, 0.1);
        }

        .form-input::placeholder {
            color: var(--text-muted);
        }

        .input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            font-size: 16px;
        }

        .input-group .form-input {
            padding-left: 44px;
        }

        .btn-primary {
            width: 100%;
            padding: 14px;
            background: var(--accent-color);
            border: none;
            border-radius: 8px;
            color: white;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 24px;
        }

        .btn-primary:hover {
            background: #0d8a6b;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 163, 127, 0.3);
        }

        .login-link {
            text-align: center;
            color: var(--text-secondary);
            font-size: 14px;
        }

        .login-link a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background: rgba(255, 107, 107, 0.1);
            border: 1px solid var(--danger-color);
            color: var(--danger-color);
        }

        .alert-success {
            background: rgba(74, 222, 128, 0.1);
            border: 1px solid var(--success-color);
            color: var(--success-color);
        }

        @media (max-width: 480px) {
            .register-container {
                padding: 24px;
                margin: 20px;
                border-radius: 12px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo-section">
            <div class="logo">
                <i class="fas fa-robot"></i>
            </div>
            <h1 class="logo-title">AI Assistant</h1>
            <p class="logo-subtitle">Create your account to get started</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i>
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-group">
                <div class="form-row">
                    <div>
                        <label class="form-label" for="first_name">First Name</label>
                        <div class="input-group">
                            <i class="input-icon fas fa-user"></i>
                            <input type="text" id="first_name" name="first_name" class="form-input" 
                                   placeholder="First name" value="{{ old('first_name') }}" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label" for="last_name">Last Name</label>
                        <div class="input-group">
                            <i class="input-icon fas fa-user"></i>
                            <input type="text" id="last_name" name="last_name" class="form-input" 
                                   placeholder="Last name" value="{{ old('last_name') }}" required>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <div class="input-group">
                    <i class="input-icon fas fa-envelope"></i>
                    <input type="email" id="email" name="email" class="form-input" 
                           placeholder="Enter your email" value="{{ old('email') }}" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <div class="input-group">
                    <i class="input-icon fas fa-lock"></i>
                    <input type="password" id="password" name="password" class="form-input" 
                           placeholder="Create a password" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirm Password</label>
                <div class="input-group">
                    <i class="input-icon fas fa-lock"></i>
                    <input type="password" id="password_confirmation" name="password_confirmation" 
                           class="form-input" placeholder="Confirm your password" required>
                </div>
            </div>

            <button type="submit" class="btn-primary">
                <span>Create Account</span>
                <i class="fas fa-arrow-right"></i>
            </button>
        </form>

        <div class="login-link">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </div>
    </div>
</body>
</html>
