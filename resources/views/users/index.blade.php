@extends('layouts.app')

@section('title', 'Users')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Users</h1>
    <a href="{{ route('users.create') }}" class="btn btn-primary">Add User</a>
</div>

<form class="row g-2 mb-3" method="GET">
    <div class="col-md-4"><input type="search" name="search" class="form-control" placeholder="Search users..." value="{{ request('search') }}"></div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Search</button></div>
</form>

<div class="card stat-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->role->label() }}</td>
                        <td><span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td class="text-end"><a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">Edit</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($users->hasPages())<div class="card-footer bg-white">{{ $users->links() }}</div>@endif
</div>
@endsection
