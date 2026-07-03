<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Global Supply Chain Risk Intelligence Platform') }}</title>

    <!-- Google Material Symbols & Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .auth-bg {
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            min-height: 100vh;
        }
        .auth-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: none;
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }
    </style>
</head>
<body class="auth-bg d-flex align-items-center justify-content-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="text-center mb-4">
                    <span class="material-symbols-outlined text-white fs-1 mb-2">public</span>
                    <h2 class="text-white fw-bold tracking-tight">GLOBAL<span class="text-accent">CHAIN</span></h2>
                    <p class="text-white-50">Enterprise Logistics Intelligence</p>
                </div>
                
                <div class="card auth-card p-4 p-md-5">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
