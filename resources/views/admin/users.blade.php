@extends('layouts.admin')

@section('content')
<main class="content-area d-flex flex-column w-100 h-100 gap-4 overflow-auto pe-auto p-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-2">
        <div>
            <h1 class="h3 fw-bold text-white mb-0 d-flex align-items-center gap-2">
                <a href="{{ route('admin.index') }}" class="text-muted text-decoration-none hover-white"><span class="material-symbols-outlined fs-4">arrow_back</span></a>
                <span class="material-symbols-outlined text-danger fs-2">manage_accounts</span> 
                User Management
            </h1>
            <p class="text-muted fs-7 mt-1 ms-5">Manage roles, passwords, and account status</p>
        </div>
        <button class="btn btn-primary d-flex align-items-center gap-2 px-3 py-2">
            <span class="material-symbols-outlined fs-5">person_add</span> Create User
        </button>
    </div>

    <!-- Search & Filter -->
    <div class="d-flex gap-2 mb-2">
        <input type="text" class="form-control bg-dark border-secondary text-white w-25" placeholder="Search users...">
        <select class="form-select bg-dark border-secondary text-white w-auto">
            <option value="">All Roles</option>
            <option value="admin">Administrator</option>
            <option value="user">User</option>
        </select>
        <button class="btn btn-outline-secondary">Search</button>
    </div>

    <x-card title="Registered Users" icon="groups" glow="purple">
        <div class="table-responsive p-3">
            <table class="table table-dark table-hover align-middle mb-0">
                <thead>
                    <tr class="border-bottom border-secondary border-opacity-25">
                        <th class="text-muted fs-8 text-uppercase pb-2">User</th>
                        <th class="text-muted fs-8 text-uppercase pb-2">Role</th>
                        <th class="text-muted fs-8 text-uppercase pb-2">Status</th>
                        <th class="text-muted fs-8 text-uppercase pb-2">Last Login</th>
                        <th class="text-muted fs-8 text-uppercase pb-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-secondary text-white fw-bold" style="width: 32px; height: 32px;">
                                    {{ substr($u->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="text-white fw-bold">{{ $u->name }}</div>
                                    <div class="text-muted fs-8">{{ $u->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            @if($u->role === 'admin')
                                <span class="badge bg-danger bg-opacity-25 text-danger border border-danger border-opacity-25">Admin</span>
                            @else
                                <span class="badge bg-secondary text-white border border-secondary border-opacity-25">User</span>
                            @endif
                        </td>
                        <td>
                            @if($u->account_status === 'active')
                                <span class="badge bg-success bg-opacity-25 text-success">Active</span>
                            @elseif($u->account_status === 'inactive')
                                <span class="badge bg-warning bg-opacity-25 text-warning">Inactive</span>
                            @else
                                <span class="badge bg-danger bg-opacity-25 text-danger">Suspended</span>
                            @endif
                        </td>
                        <td class="text-muted fs-8">{{ $u->last_login_at ? $u->last_login_at->diffForHumans() : 'Never' }}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary py-0 px-2" title="Edit"><span class="material-symbols-outlined fs-6">edit</span></button>
                            <button class="btn btn-sm btn-outline-warning py-0 px-2" title="Reset Password"><span class="material-symbols-outlined fs-6">key</span></button>
                            <button class="btn btn-sm btn-outline-danger py-0 px-2" title="Deactivate"><span class="material-symbols-outlined fs-6">block</span></button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </x-card>
</main>
@endsection
