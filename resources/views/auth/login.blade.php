<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-success" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <h4 class="fw-bold text-white mb-4 text-center">Sign In</h4>
        
        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label text-muted fw-medium fs-7 text-uppercase">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 border-secondary text-muted">
                    <span class="material-symbols-outlined fs-5">mail</span>
                </span>
                <input id="email" class="form-control bg-transparent text-white border-start-0 border-secondary py-2 @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="user@example.com">
            </div>
            @error('email')
                <div class="text-danger mt-1 fs-7">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <label for="password" class="form-label text-muted fw-medium fs-7 text-uppercase mb-0">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-primary text-decoration-none fs-7 fw-medium" href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 border-secondary text-muted">
                    <span class="material-symbols-outlined fs-5">lock</span>
                </span>
                <input id="password" class="form-control bg-transparent text-white border-start-0 border-secondary py-2 @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            </div>
            @error('password')
                <div class="text-danger mt-1 fs-7">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-4 form-check">
            <input id="remember_me" type="checkbox" class="form-check-input bg-transparent border-secondary" name="remember">
            <label for="remember_me" class="form-check-label text-muted fs-7">
                {{ __('Remember me') }}
            </label>
        </div>

        <button class="btn btn-primary w-100 py-2 mb-3 rounded-pill fw-medium shadow-sm d-flex justify-content-center align-items-center">
            Sign In <span class="material-symbols-outlined ms-2 fs-5">arrow_forward</span>
        </button>
        
        @if (Route::has('register'))
            <p class="text-center text-muted mb-0 fs-7">
                Don't have an account? <a href="{{ route('register') }}" class="text-primary text-decoration-none fw-medium">Register now</a>
            </p>
        @endif
    </form>
</x-guest-layout>
