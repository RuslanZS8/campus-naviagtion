<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ __('campus.title') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />

    <!-- Styles -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Figtree', sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .welcome-container {
            text-align: center;
            padding: 40px;
            max-width: 800px;
            width: 100%;
        }

        .logo-container {
            margin-bottom: 40px;
        }

        .logo-container img {
            height: 80px;
            width: auto;
            margin-bottom: 20px;
        }

        .logo-container h1 {
            color: #ffffff;
            font-size: 2.5rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }

        .logo-container p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 1.1rem;
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 40px 0;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 24px 20px;
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .feature-card .icon {
            font-size: 2.5rem;
            margin-bottom: 12px;
        }

        .feature-card h3 {
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .feature-card p {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.85rem;
        }

        .btn-enter {
            display: inline-block;
            background: linear-gradient(135deg, #2a6df4, #4a8df4);
            color: #fff;
            padding: 16px 48px;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(42, 109, 244, 0.3);
            border: none;
            cursor: pointer;
        }

        .btn-enter:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 30px rgba(42, 109, 244, 0.5);
        }

        .btn-enter:active {
            transform: scale(0.95);
        }

        .language-selector {
            margin-top: 30px;
            display: flex;
            gap: 12px;
            justify-content: center;
        }

        .language-selector a {
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            padding: 6px 14px;
            border-radius: 6px;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .language-selector a:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.05);
        }

        .language-selector a.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .language-selector .divider {
            color: rgba(255, 255, 255, 0.2);
            font-size: 0.9rem;
        }

        .footer {
            margin-top: 40px;
            color: rgba(255, 255, 255, 0.3);
            font-size: 0.8rem;
        }

        @media (max-width: 600px) {
            .logo-container h1 {
                font-size: 1.8rem;
            }

            .features {
                grid-template-columns: 1fr 1fr;
                gap: 12px;
            }

            .feature-card {
                padding: 16px;
            }

            .btn-enter {
                padding: 14px 32px;
                font-size: 1rem;
            }
        }

        @media (max-width: 400px) {
            .features {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="welcome-container">
        <!-- Logo & Title -->
        <div class="logo-container">
            <img src="https://www.ciu.edu.tr/map/logo_en.svg" alt="CIU Logo">
            <h1>{{ __('campus.title') }}</h1>
            <p>{{ __('campus.welcome_message') ?? 'Find your way around campus with ease.' }}</p>
        </div>

        <!-- Features -->
        <div class="features">
            <div class="feature-card">
                <div class="icon">🗺️</div>
                <h3>{{ __('campus.features.interactive_map') ?? 'Interactive Map' }}</h3>
                <p>{{ __('campus.features.interactive_desc') ?? 'Explore campus buildings in real-time' }}</p>
            </div>
            <div class="feature-card">
                <div class="icon">📍</div>
                <h3>{{ __('campus.features.route_planning') ?? 'Route Planning' }}</h3>
                <p>{{ __('campus.features.route_desc') ?? 'Find the best path between locations' }}</p>
            </div>
            <div class="feature-card">
                <div class="icon">♿</div>
                <h3>{{ __('campus.features.accessibility') ?? 'Accessibility' }}</h3>
                <p>{{ __('campus.features.accessibility_desc') ?? 'Accessible routes for everyone' }}</p>
            </div>
            <div class="feature-card">
                <div class="icon">📱</div>
                <h3>{{ __('campus.features.real_time') ?? 'Real-Time' }}</h3>
                <p>{{ __('campus.features.real_time_desc') ?? 'Live updates and building status' }}</p>
            </div>
        </div>

        <!-- Enter Button -->
        <a href="{{ route('home') }}" class="btn-enter">
            {{ __('campus.enter_button') ?? '🚀 Enter Campus Navigation' }}
        </a>

        <!-- Language Selector -->
        <div class="language-selector">
            <a href="{{ url()->current() }}?lang=en" class="{{ app()->getLocale() == 'en' ? 'active' : '' }}">EN</a>
            <span class="divider">|</span>
            <a href="{{ url()->current() }}?lang=tr" class="{{ app()->getLocale() == 'tr' ? 'active' : '' }}">TR</a>
        </div>

        <div class="footer">
            {{ __('campus.sidebar.powered_by') }}
        </div>
    </div>
</body>

</html>