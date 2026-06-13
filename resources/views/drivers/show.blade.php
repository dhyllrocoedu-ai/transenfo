@extends('layouts.app')

@section('title', $driver->fullName())

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h3 mb-1">{{ $driver->fullName() }}</h1>
        <p class="text-muted mb-0">License: {{ $driver->license_number }}</p>
    </div>
    @can('update', $driver)
        <a href="{{ route('drivers.edit', $driver) }}" class="btn btn-outline-primary">Edit</a>
    @endcan
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card stat-card">
            <div class="card-header bg-white"><strong>Driver Information</strong></div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt class="text-muted small">Phone</dt><dd>{{ $driver->phone ?? '—' }}</dd>
                    <dt class="text-muted small">Email</dt><dd>{{ $driver->email ?? '—' }}</dd>
                    <dt class="text-muted small">License Expiry</dt><dd>{{ $driver->license_expiry?->format('M d, Y') ?? '—' }}</dd>
                    <dt class="text-muted small">Address</dt><dd>{{ $driver->address ?? '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card stat-card mb-4">
            <div class="card-header bg-white"><strong>Vehicles</strong></div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Plate</th><th>Classification</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($driver->vehicles as $vehicle)
                            <tr>
                                <td><a href="{{ route('vehicles.show', $vehicle) }}">{{ $vehicle->plate_number }}</a></td>
                                <td>{{ $vehicle->classification }}</td>
                                <td>{{ ucfirst($vehicle->registration_status) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted text-center py-3">No vehicles linked.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card stat-card">
            <div class="card-header bg-white"><strong>Citation History</strong></div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Citation #</th><th>Violation</th><th>Date</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($driver->citations as $citation)
                            <tr>
                                <td><a href="{{ route('citations.show', $citation) }}">{{ $citation->citation_number }}</a></td>
                                <td>{{ $citation->violationType->name }}</td>
                                <td>{{ $citation->issued_at->format('M d, Y') }}</td>
                                <td><span class="badge {{ $citation->status->badgeClass() }}">{{ $citation->status->label() }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted text-center py-3">No citations on record.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
