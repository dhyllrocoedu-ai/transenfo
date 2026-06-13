@extends('layouts.app')

@section('title', 'Drivers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Drivers</h1>
    @can('create', App\Models\Driver::class)
        <a href="{{ route('drivers.create') }}" class="btn btn-primary">Register Driver</a>
    @endcan
</div>

<form class="row g-2 mb-3" method="GET">
    <div class="col-md-4">
        <input type="search" name="search" class="form-control" placeholder="Search name or license..." value="{{ request('search') }}">
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-secondary">Search</button>
    </div>
</form>

<div class="card stat-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>License #</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($drivers as $driver)
                    <tr>
                        <td>{{ $driver->fullName() }}</td>
                        <td>{{ $driver->license_number }}</td>
                        <td>{{ $driver->phone ?? '—' }}</td>
                        <td>{{ $driver->email ?? '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('drivers.show', $driver) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No drivers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($drivers->hasPages())
        <div class="card-footer bg-white">{{ $drivers->links() }}</div>
    @endif
</div>
@endsection
