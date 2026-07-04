<section>
    <header>
        <p class="mt-1 text-muted fs-7">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('put')

        <div class="mb-3">
            <label for="update_password_current_password" class="form-label text-muted fs-7 fw-medium text-uppercase">{{ __('Current Password') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" class="form-control bg-transparent text-white border-secondary @if($errors->updatePassword->has('current_password')) is-invalid @endif" autocomplete="current-password" />
            @if($errors->updatePassword->has('current_password'))
                <div class="invalid-feedback">{{ $errors->updatePassword->first('current_password') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="update_password_password" class="form-label text-muted fs-7 fw-medium text-uppercase">{{ __('New Password') }}</label>
            <input id="update_password_password" name="password" type="password" class="form-control bg-transparent text-white border-secondary @if($errors->updatePassword->has('password')) is-invalid @endif" autocomplete="new-password" />
            @if($errors->updatePassword->has('password'))
                <div class="invalid-feedback">{{ $errors->updatePassword->first('password') }}</div>
            @endif
        </div>

        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label text-muted fs-7 fw-medium text-uppercase">{{ __('Confirm Password') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" class="form-control bg-transparent text-white border-secondary @if($errors->updatePassword->has('password_confirmation')) is-invalid @endif" autocomplete="new-password" />
            @if($errors->updatePassword->has('password_confirmation'))
                <div class="invalid-feedback">{{ $errors->updatePassword->first('password_confirmation') }}</div>
            @endif
        </div>

        <div class="d-flex align-items-center gap-3 mt-4">
            <button class="btn btn-primary rounded-pill px-4 fw-medium">{{ __('Update Password') }}</button>

            @if (session('status') === 'password-updated')
                <span class="text-success fs-7 fw-bold">{{ __('Password saved.') }}</span>
            @endif
        </div>
    </form>
</section>
