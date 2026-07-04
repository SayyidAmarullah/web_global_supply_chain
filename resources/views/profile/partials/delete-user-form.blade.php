<section>
    <header>
        <p class="mt-1 text-muted fs-7">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted.') }}
        </p>
    </header>

    <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-medium mt-3" data-bs-toggle="modal" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        {{ __('Delete Account') }}
    </button>

    <!-- Modal -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content glass-panel border-danger">
                <form method="post" action="{{ route('profile.destroy') }}">
                    @csrf
                    @method('delete')
                    
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title text-white">{{ __('Confirm Account Deletion') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <div class="modal-body text-white">
                        <p class="text-muted fs-7">{{ __('Please enter your password to confirm you would like to permanently delete your account.') }}</p>
                        <div class="mb-3 mt-3">
                            <label for="password" class="form-label text-muted fs-7 fw-medium text-uppercase">{{ __('Password') }}</label>
                            <input id="password" name="password" type="password" class="form-control bg-transparent text-white border-secondary @if($errors->userDeletion->has('password')) is-invalid @endif" placeholder="{{ __('Password') }}" />
                            @if($errors->userDeletion->has('password'))
                                <div class="invalid-feedback d-block">{{ $errors->userDeletion->first('password') }}</div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4">{{ __('Delete Account') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@if($errors->userDeletion->isNotEmpty())
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var myModal = new bootstrap.Modal(document.getElementById('confirmUserDeletionModal'));
            myModal.show();
        });
    </script>
@endif
