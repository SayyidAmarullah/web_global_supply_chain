<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4 text-success" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <h4 class="fw-bold text-secondary mb-4 text-center">Sign In</h4>
        
        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label text-muted fw-medium fs-7 text-uppercase">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                    <span class="material-symbols-outlined fs-5 text-muted">mail</span>
                </span>
                <input id="email" class="form-control bg-light border-start-0 py-2 @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
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
                <span class="input-group-text bg-light border-end-0">
                    <span class="material-symbols-outlined fs-5 text-muted">lock</span>
                </span>
                <input id="password" class="form-control bg-light border-start-0 py-2 @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="current-password">
            </div>
            @error('password')
                <div class="text-danger mt-1 fs-7">{{ $message }}</div>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="mb-4 form-check">
            <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
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
