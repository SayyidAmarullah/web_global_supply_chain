@extends('layouts.app')

@section('content')
    <div class="content-area p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-white fw-bold mb-0">Profile Settings</h4>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="glass-panel p-4 h-100">
                    <h5 class="text-white fw-bold mb-4">Profile Information</h5>
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="col-md-6">
                <div class="glass-panel p-4 h-100">
                    <h5 class="text-white fw-bold mb-4">Update Password</h5>
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="col-md-12">
                <div class="glass-panel p-4 border-danger border-opacity-25">
                    <h5 class="text-danger fw-bold mb-4">Danger Zone</h5>
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
@endsection
