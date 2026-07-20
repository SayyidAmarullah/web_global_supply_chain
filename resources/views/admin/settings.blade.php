@extends('layouts.admin')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="h3 fw-bold text-white mb-0 d-flex align-items-center gap-2">
                <a href="{{ route('admin.index') }}" class="text-muted text-decoration-none hover-white"><span class="material-symbols-outlined fs-4">arrow_back</span></a>
                <span class="material-symbols-outlined text-purple-neon fs-2">settings</span> 
                System Configuration
            </h1>
            <p class="text-muted fs-7 mt-1 ms-5">Global settings, backups, and environment configurations</p>
        </div>
        <button class="btn btn-success d-flex align-items-center gap-2 px-3 py-2" onclick="document.getElementById('settingsForm').submit();">
            <span class="material-symbols-outlined fs-5">save</span> Save Changes
        </button>
    </div>

    <div class="row g-4">
        <!-- General Settings -->
        <div class="col-md-6">
            <x-card title="General Settings" icon="tune" glow="purple">
                <div class="p-4">
                    <form action="{{ route('admin.settings.update') }}" method="POST" id="settingsForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-muted fs-8 text-uppercase">Application Name</label>
                            <input type="text" name="app_name" class="form-control bg-dark border-secondary text-white" value="Global Supply Chain Platform">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted fs-8 text-uppercase">Timezone</label>
                            <select name="timezone" class="form-select bg-dark border-secondary text-white">
                                <option value="UTC" selected>UTC (Coordinated Universal Time)</option>
                                <option value="EST">EST (Eastern Standard Time)</option>
                                <option value="GMT">GMT (Greenwich Mean Time)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted fs-8 text-uppercase">Default Theme</label>
                            <select name="theme" class="form-select bg-dark border-secondary text-white">
                                <option value="dark" selected>Aurora Dark Mode</option>
                                <option value="light">Aurora Light Mode</option>
                            </select>
                        </div>
                        <div class="mb-0">
                            <label class="form-label text-muted fs-8 text-uppercase">Max File Upload Size</label>
                            <div class="input-group">
                                <input type="number" name="max_upload" class="form-control bg-dark border-secondary text-white" value="50">
                                <span class="input-group-text bg-dark border-secondary text-muted">MB</span>
                            </div>
                        </div>
                    </form>
                </div>
            </x-card>
        </div>

        <!-- Backup Management -->
        <div class="col-md-6">
            <x-card title="Backup Management" icon="cloud_download" glow="cyan">
                <div class="p-4">
                    <p class="text-muted fs-7 mb-4">Manage database and application backups. Automatic backups are currently enabled.</p>
                    
                    <div class="d-flex justify-content-between align-items-center p-3 border border-secondary border-opacity-25 rounded mb-3 bg-dark">
                        <div>
                            <div class="text-white fw-bold">Database Backup</div>
                            <div class="text-muted fs-8">Last backup: Today, 03:00 UTC</div>
                        </div>
                        <button class="btn btn-sm btn-primary">Backup Now</button>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-3 border border-secondary border-opacity-25 rounded mb-3 bg-dark">
                        <div>
                            <div class="text-white fw-bold">Full Application Backup</div>
                            <div class="text-muted fs-8">Last backup: Sunday, 00:00 UTC</div>
                        </div>
                        <button class="btn btn-sm btn-outline-primary">Backup Now</button>
                    </div>

                    <h6 class="text-white fw-bold mt-4 mb-2">Recent Backups</h6>
                    <ul class="list-group list-group-flush border border-secondary border-opacity-25 rounded">
                        <li class="list-group-item bg-dark text-white d-flex justify-content-between align-items-center">
                            <span class="fs-7"><span class="material-symbols-outlined fs-7 text-success me-2 align-middle">check_circle</span> db_backup_2026_07_14.sql.gz</span>
                            <button class="btn btn-sm btn-link text-cyan-glow p-0"><span class="material-symbols-outlined fs-5">download</span></button>
                        </li>
                        <li class="list-group-item bg-dark text-white d-flex justify-content-between align-items-center">
                            <span class="fs-7"><span class="material-symbols-outlined fs-7 text-success me-2 align-middle">check_circle</span> app_backup_2026_07_12.zip</span>
                            <button class="btn btn-sm btn-link text-cyan-glow p-0"><span class="material-symbols-outlined fs-5">download</span></button>
                        </li>
                    </ul>
                </div>
            </x-card>
        </div>
    </div>
</main>
@endsection
