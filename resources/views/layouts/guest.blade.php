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
            background-color: var(--secondary);
            background-image: radial-gradient(circle at 100% 0%, rgba(14, 165, 233, 0.15) 0%, transparent 40%),
                              radial-gradient(circle at 0% 100%, rgba(14, 165, 233, 0.05) 0%, transparent 40%);
            min-height: 100vh;
        }
        .auth-card {
            background: #041326;
            border: 1px solid rgba(14, 165, 233, 0.2);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
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
