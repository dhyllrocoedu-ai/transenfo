@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Users</h1>
    <a href="{{ route('users.create') }}" class="btn btn-primary">Add User</a>
</div>

<form class="row g-2 mb-3" method="GET">
    <div class="col-md-3"><input type="search" name="search" class="form-control" placeholder="Search users..." value="{{ request('search') }}"></div>
    <div class="col-md-2">
        <select name="account_status" class="form-select">
            <option value="">All Status</option>
            <option value="pending" {{ request('account_status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('account_status') === 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('account_status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            <option value="suspended" {{ request('account_status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
        </select>
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Filter</button></div>
</form>

<div class="card stat-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Account Status</th><th>Active</th><th></th></tr></thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="{{ $user->account_status === 'pending' ? 'table-warning' : ($user->account_status === 'rejected' ? 'table-danger' : '') }}">
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role->label() }}</td>
                        <td>
                            @php
                                $statusBadge = match($user->account_status) {
                                    'approved' => ['bg-success', 'Approved'],
                                    'pending' => ['bg-warning text-dark', 'Pending'],
                                    'rejected' => ['bg-danger', 'Rejected'],
                                    'suspended' => ['bg-secondary', 'Suspended'],
                                    default => ['bg-info', ucfirst($user->account_status ?? 'Unknown')],
                                };
                            @endphp
                            <span class="badge {{ $statusBadge[0] }}">{{ $statusBadge[1] }}</span>
                        </td>
                        <td><span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $user->is_active ? 'Yes' : 'No' }}</span></td>
                        <td class="text-end">
                            <div class="d-flex gap-1 justify-content-end">
                                @if ($user->account_status === 'pending')
                                    <form method="POST" action="{{ route('users.approve', $user) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm btn-success" title="Approve"><i class="bi bi-check-lg"></i></button>
                                    </form>
                                    <button class="btn btn-sm btn-danger" title="Reject" data-bs-toggle="modal" data-bs-target="#rejectModal-{{ $user->id }}"><i class="bi bi-x-lg"></i></button>
                                @endif
                                @if ($user->account_status !== 'pending')
                                    <form method="POST" action="{{ route('users.toggle-status', $user) }}" class="d-inline">
                                        @csrf
                                        <button class="btn btn-sm {{ $user->account_status === 'suspended' ? 'btn-success' : 'btn-warning' }}" title="{{ $user->account_status === 'suspended' ? 'Unsuspend' : 'Suspend' }}">
                                            <i class="bi {{ $user->account_status === 'suspended' ? 'bi-unlock' : 'bi-lock' }}"></i>
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('users.devices', $user) }}" class="btn btn-sm btn-outline-secondary" title="Devices"><i class="bi bi-phone"></i></a>
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            </div>
                        </td>
                    </tr>

                    {{-- Reject Modal --}}
                    @if ($user->account_status === 'pending')
                        <div class="modal fade" id="rejectModal-{{ $user->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <form method="POST" action="{{ route('users.reject', $user) }}" class="modal-content">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title">Reject User: {{ $user->name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label class="form-label">Reason for rejection</label>
                                        <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="Explain why this registration was rejected..."></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger">Reject User</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($users->hasPages())<div class="card-footer bg-white">{{ $users->links() }}</div>@endif
</div>
@endsection
