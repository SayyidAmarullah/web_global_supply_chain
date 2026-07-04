<section>
    <header>
        <p class="mt-1 text-muted fs-7">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-4">
        @csrf
        @method('patch')

        <div class="mb-3">
            <label for="name" class="form-label text-muted fs-7 fw-medium text-uppercase">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" class="form-control bg-transparent text-white border-secondary @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name" />
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label text-muted fs-7 fw-medium text-uppercase">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" class="form-control bg-transparent text-white border-secondary @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required autocomplete="username" />
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="phone_number" class="form-label text-muted fs-7 fw-medium text-uppercase">{{ __('Phone') }}</label>
                <input id="phone_number" name="phone_number" type="text" class="form-control bg-transparent text-white border-secondary @error('phone_number') is-invalid @enderror" value="{{ old('phone_number', $user->phone_number) }}" />
            </div>
            <div class="col-md-6 mb-3">
                <label for="company_name" class="form-label text-muted fs-7 fw-medium text-uppercase">{{ __('Company') }}</label>
                <input id="company_name" name="company_name" type="text" class="form-control bg-transparent text-white border-secondary @error('company_name') is-invalid @enderror" value="{{ old('company_name', $user->company_name) }}" />
            </div>
        </div>

        <div class="d-flex align-items-center gap-3 mt-4">
            <button class="btn btn-primary rounded-pill px-4 fw-medium">{{ __('Save Changes') }}</button>

            @if (session('status') === 'profile-updated')
                <span class="text-success fs-7 fw-bold">{{ __('Saved successfully.') }}</span>
            @endif
        </div>
    </form>
</section>
