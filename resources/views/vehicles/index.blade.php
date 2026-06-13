@extends('layouts.app')

@section('title', 'Vehicles')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Vehicles</h1>
    @can('create', App\Models\Vehicle::class)
        <a href="{{ route('vehicles.create') }}" class="btn btn-primary">Register Vehicle</a>
    @endcan
</div>

<form class="row g-2 mb-3" method="GET">
    <div class="col-md-4">
        <input type="search" name="search" class="form-control" placeholder="Search plate number..." value="{{ request('search') }}">
    </div>
    <div class="col-auto"><button class="btn btn-outline-secondary">Search</button></div>
</form>

<div class="card stat-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Plate #</th>
                    <th>Classification</th>
                    <th>Owner</th>
                    <th>Driver</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($vehicles as $vehicle)
                    <tr>
                        <td><strong>{{ $vehicle->plate_number }}</strong></td>
                        <td>{{ $vehicle->classification }}</td>
                        <td>{{ $vehicle->owner?->name ?? '—' }}</td>
                        <td>{{ $vehicle->driver?->fullName() ?? '—' }}</td>
                        <td>{{ ucfirst($vehicle->registration_status) }}</td>
                        <td class="text-end">
                            <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No vehicles found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($vehicles->hasPages())
        <div class="card-footer bg-white">{{ $vehicles->links() }}</div>
    @endif
</div>
@endsection
