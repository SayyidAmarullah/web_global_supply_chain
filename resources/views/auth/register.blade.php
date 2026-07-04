<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <h4 class="fw-bold text-white mb-4 text-center">Create Account</h4>
        
        <!-- Name -->
        <div class="mb-4">
            <label for="name" class="form-label text-muted fw-medium fs-7 text-uppercase">Full Name</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 border-secondary text-muted">
                    <span class="material-symbols-outlined fs-5">person</span>
                </span>
                <input id="name" class="form-control bg-transparent text-white border-start-0 border-secondary py-2 @error('name') is-invalid @enderror" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="John Doe">
            </div>
            @error('name')
                <div class="text-danger mt-1 fs-7">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email Address -->
        <div class="mb-4">
            <label for="email" class="form-label text-muted fw-medium fs-7 text-uppercase">Email Address</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 border-secondary text-muted">
                    <span class="material-symbols-outlined fs-5">mail</span>
                </span>
                <input id="email" class="form-control bg-transparent text-white border-start-0 border-secondary py-2 @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="user@example.com">
            </div>
            @error('email')
                <div class="text-danger mt-1 fs-7">{{ $message }}</div>
            @enderror
        </div>

        <!-- Password -->
        <div class="mb-4">
            <label for="password" class="form-label text-muted fw-medium fs-7 text-uppercase">Password</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 border-secondary text-muted">
                    <span class="material-symbols-outlined fs-5">lock</span>
                </span>
                <input id="password" class="form-control bg-transparent text-white border-start-0 border-secondary py-2 @error('password') is-invalid @enderror" type="password" name="password" required autocomplete="new-password" placeholder="••••••••">
            </div>
            @error('password')
                <div class="text-danger mt-1 fs-7">{{ $message }}</div>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label text-muted fw-medium fs-7 text-uppercase">Confirm Password</label>
            <div class="input-group">
                <span class="input-group-text bg-transparent border-end-0 border-secondary text-muted">
                    <span class="material-symbols-outlined fs-5">lock_reset</span>
                </span>
                <input id="password_confirmation" class="form-control bg-transparent text-white border-start-0 border-secondary py-2 @error('password_confirmation') is-invalid @enderror" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="••••••••">
            </div>
            @error('password_confirmation')
                <div class="text-danger mt-1 fs-7">{{ $message }}</div>
            @enderror
        </div>

        <button class="btn btn-primary w-100 py-2 mb-3 rounded-pill fw-medium shadow-sm d-flex justify-content-center align-items-center">
            Register <span class="material-symbols-outlined ms-2 fs-5">person_add</span>
        </button>
        
        <p class="text-center text-muted mb-0 fs-7">
            Already registered? <a href="{{ route('login') }}" class="text-primary text-decoration-none fw-medium">Sign In</a>
        </p>
    </form>
</x-guest-layout>
