<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Global Supply Chain Risk Intelligence Platform') }}</title>

    <!-- Google Material Symbols & Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        :root {
            --glass: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.12);
            --glass-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            --bg: #050816;
            --primary: #3B82F6;
            --secondary: #06B6D4;
            --accent: #8B5CF6;
            --highlight: #10B981;
        }
        html, body {
            background: var(--bg);
            font-family: 'Inter', sans-serif;
            color: white;
            min-height: 100vh;
            height: auto !important;
            overflow-x: hidden;
            overflow-y: auto !important;
        }
        .aurora-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(135deg, #050816 0%, #0a0a2e 25%, #0f0f3a 50%, #0a0a2e 75%, #050816 100%);
        }
        .aurora-bg::before {
            content: '';
            position: absolute;
            top: -20%;
            left: -20%;
            width: 140%;
            height: 140%;
            background: radial-gradient(ellipse at 20% 30%, rgba(59,130,246,0.15) 0%, transparent 50%),
                        radial-gradient(ellipse at 80% 20%, rgba(6,182,212,0.12) 0%, transparent 45%),
                        radial-gradient(ellipse at 50% 70%, rgba(139,92,246,0.10) 0%, transparent 50%),
                        radial-gradient(ellipse at 30% 80%, rgba(16,185,129,0.08) 0%, transparent 40%);
            animation: aurora 15s ease-in-out infinite alternate;
            pointer-events: none;
        }
        @keyframes aurora {
            0% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(-5%, 3%) scale(1.05); }
            50% { transform: translate(2%, -2%) scale(1.02); }
            75% { transform: translate(-3%, 5%) scale(1.08); }
            100% { transform: translate(5%, -3%) scale(1.03); }
        }
        .auth-card {
            background: var(--glass);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: var(--glass-shadow);
        }
        .form-control, .input-group-text {
            border-color: rgba(255, 255, 255, 0.1) !important;
            background: rgba(0, 0, 0, 0.2) !important;
            color: white !important;
        }
        .form-control:focus {
            background: rgba(0, 0, 0, 0.4) !important;
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 0.25rem rgba(59, 130, 246, 0.25) !important;
            color: white !important;
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5) !important;
        }
        .btn-gradient {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            border: none;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-gradient:hover {
            opacity: 0.9;
            box-shadow: 0 0 15px rgba(59, 130, 246, 0.4);
            transform: translateY(-1px);
            color: white;
        }
    </style>
</head>
<body class="min-vh-100 py-5">
    <div class="aurora-bg"></div>
    <div class="container relative z-10">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-4">
                <div class="text-center mb-4">
                    <span class="material-symbols-outlined fs-1 mb-2" style="color: #38bdf8;">hub</span>
                    <h2 class="text-white fw-bold tracking-tight mb-1" style="letter-spacing: 0.5px;">GLOBAL<span style="color: #38bdf8; font-weight: 300;">CHAIN</span></h2>
                    <p class="text-white-50">Enterprise Logistics Intelligence</p>
                </div>
                
                <div class="card auth-card p-4 p-md-5 border-0">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
