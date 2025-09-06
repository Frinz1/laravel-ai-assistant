<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }} - AI Assistant Platform</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            --hover-bg: #343541;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--primary-bg) 0%, #1a1a3e 100%);
            color: var(--text-primary);
            min-height: 100vh;
        }

        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        .hero-content {
            max-width: 800px;
            z-index: 2;
        }

        .logo {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 32px;
            font-size: 48px;
            color: white;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 24px;
            background: linear-gradient(135deg, var(--text-primary) 0%, var(--accent-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--text-secondary);
            margin-bottom: 48px;
            line-height: 1.6;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 16px 32px;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: var(--accent-color);
            color: white;
            border: 2px solid var(--accent-color);
        }

        .btn-primary:hover {
            background: #0d8a6b;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 163, 127, 0.3);
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-primary);
            border: 2px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--hover-bg);
            border-color: var(--accent-color);
        }

        .features-section {
            padding: 100px 20px;
            background: var(--secondary-bg);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 60px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
        }

        .feature-card {
            background: var(--accent-bg);
            padding: 40px;
            border-radius: 16px;
            border: 1px solid var(--border-color);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--accent-color), #0d8a6b);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
            margin-bottom: 24px;
        }

        .feature-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .feature-description {
            color: var(--text-secondary);
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 100%;
                max-width: 300px;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <section class="hero-section">
        <div class="hero-content">
            <div class="logo">
                <i class="fas fa-robot"></i>
            </div>
            <h1 class="hero-title">Welcome to AI Assistant</h1>
            <p class="hero-subtitle">
                Experience the future of AI-powered conversations. Get intelligent responses, 
                creative solutions, and personalized assistance tailored to your needs.
            </p>
            <div class="cta-buttons">
                <a href="{{ route('register') }}" class="btn btn-primary">
                    <i class="fas fa-rocket"></i>
                    Get Started Free
                </a>
                <a href="{{ route('login') }}" class="btn btn-secondary">
                    <i class="fas fa-sign-in-alt"></i>
                    Sign In
                </a>
            </div>
        </div>
    </section>

    <section class="features-section">
        <div class="container">
            <h2 class="section-title">Why Choose Our AI Assistant?</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3 class="feature-title">Advanced AI</h3>
                    <p class="feature-description">
                        Powered by cutting-edge AI technology that understands context and provides intelligent responses.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <h3 class="feature-title">Natural Conversations</h3>
                    <p class="feature-description">
                        Engage in natural, flowing conversations that feel human-like and contextually aware.
                    </p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3 class="feature-title">Secure & Private</h3>
                    <p class="feature-description">
                        Your conversations are encrypted and private. We prioritize your data security above all.
                    </p>
                </div>
            </div>
        </div>
    </section>
</body>
</html>
