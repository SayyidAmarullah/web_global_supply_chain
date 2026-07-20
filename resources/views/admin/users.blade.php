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
                            @elseif($u->role === 'importer')
                                <span class="badge bg-info bg-opacity-25 text-info border border-info border-opacity-25">Importir</span>
                            @elseif($u->role === 'exporter')
                                <span class="badge bg-primary bg-opacity-25 text-primary border border-primary border-opacity-25">Eksportir</span>
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
                            <button class="btn btn-sm btn-outline-secondary py-0 px-2" title="Edit" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $u->id }}">
                                <span class="material-symbols-outlined fs-6">edit</span>
                            </button>
                            <form action="{{ route('admin.users.delete', $u->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Delete" {{ $u->id === auth()->id() ? 'disabled' : '' }}>
                                    <span class="material-symbols-outlined fs-6">delete</span>
                                </button>
                            </form>

                            <!-- Edit User Modal -->
                            <div class="modal fade" id="editUserModal{{ $u->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content bg-dark border-secondary">
                                        <form action="{{ route('admin.users.update', $u->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-header border-secondary border-opacity-25">
                                                <h5 class="modal-title text-white">Edit User: {{ $u->name }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label text-muted">Role</label>
                                                    <select name="role" class="form-select bg-dark border-secondary text-white">
                                                        <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Administrator</option>
                                                        <option value="importer" {{ $u->role === 'importer' ? 'selected' : '' }}>Importer</option>
                                                        <option value="exporter" {{ $u->role === 'exporter' ? 'selected' : '' }}>Exporter</option>
                                                        <option value="user" {{ $u->role === 'user' ? 'selected' : '' }}>Standard User</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label text-muted">Account Status</label>
                                                    <select name="account_status" class="form-select bg-dark border-secondary text-white">
                                                        <option value="active" {{ $u->account_status === 'active' ? 'selected' : '' }}>Active</option>
                                                        <option value="inactive" {{ $u->account_status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                        <option value="suspended" {{ $u->account_status === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-secondary border-opacity-25">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
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
