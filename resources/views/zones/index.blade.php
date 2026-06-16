@extends('layouts.app')

@section('title', 'Zone Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate-on-load">
    <div>
        <h1 class="h3 mb-1">Zone Management</h1>
        <p class="text-muted mb-0">Define patrol zones and assign each one to a response team.</p>
    </div>
    <a href="{{ route('zones.create') }}" class="btn btn-primary">Create Zone</a>
</div>

<div class="row g-4">
    @forelse ($zones as $zone)
        <div class="col-lg-6">
            <div class="card stat-card h-100 animate-on-load">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <h5 class="mb-1">{{ $zone->name }}</h5>
                            <p class="text-muted small mb-0">{{ $zone->description ?: 'No description provided.' }}</p>
                        </div>
                        <span class="badge {{ $zone->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $zone->is_active ? 'Active' : 'Inactive' }}</span>
                    </div>
                    <div class="mt-3 small text-muted">
                        <div><strong>Assigned Team:</strong> {{ $zone->team?->name ?? 'Unassigned' }}</div>
                        <div><strong>Center:</strong> {{ $zone->center_latitude }}, {{ $zone->center_longitude }}</div>
                        <div><strong>Radius:</strong> {{ $zone->radius_m }} m</div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('zones.edit', $zone) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12"><div class="card stat-card"><div class="card-body text-muted">No zones created yet.</div></div></div>
    @endforelse
</div>
@endsection
