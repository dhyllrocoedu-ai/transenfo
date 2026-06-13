@extends('layouts.app')

@section('title', $vehicle->plate_number)

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h3 mb-1">{{ $vehicle->plate_number }}</h1>
        <p class="text-muted mb-0">{{ $vehicle->make }} {{ $vehicle->model }} · {{ $vehicle->classification }}</p>
    </div>
    <div class="d-flex gap-2">
        @can('create', App\Models\Citation::class)
            <a href="{{ route('citations.create', ['vehicle_id' => $vehicle->id]) }}" class="btn btn-danger">Issue Citation</a>
        @endcan
        @can('update', $vehicle)
            <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-outline-primary">Edit</a>
        @endcan
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card stat-card">
            <div class="card-header bg-white"><strong>Vehicle Details</strong></div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt class="text-muted small">Owner</dt><dd>{{ $vehicle->owner?->name ?? '—' }}</dd>
                    <dt class="text-muted small">Driver</dt><dd>{{ $vehicle->driver?->fullName() ?? '—' }}</dd>
                    <dt class="text-muted small">Color</dt><dd>{{ $vehicle->color ?? '—' }}</dd>
                    <dt class="text-muted small">Year</dt><dd>{{ $vehicle->year ?? '—' }}</dd>
                    <dt class="text-muted small">Registration</dt><dd>{{ ucfirst($vehicle->registration_status) }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card stat-card mb-4">
            <div class="card-header bg-white"><strong>Enforcement History</strong></div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead><tr><th>Citation #</th><th>Violation</th><th>Amount</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse ($vehicle->citations as $citation)
                            <tr>
                                <td><a href="{{ route('citations.show', $citation) }}">{{ $citation->citation_number }}</a></td>
                                <td>{{ $citation->violationType->name }}</td>
                                <td>₱{{ number_format($citation->penalty_amount, 2) }}</td>
                                <td><span class="badge {{ $citation->status->badgeClass() }}">{{ $citation->status->label() }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted text-center py-3">No enforcement history.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($vehicle->clampingRecords->isNotEmpty())
            <div class="card stat-card">
                <div class="card-header bg-white"><strong>Clamping Records</strong></div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th>Notice #</th><th>Date</th><th>Status</th></tr></thead>
                        <tbody>
                            @foreach ($vehicle->clampingRecords as $record)
                                <tr>
                                    <td><a href="{{ route('clamping.show', $record) }}">{{ $record->notice_number }}</a></td>
                                    <td>{{ $record->clamped_at->format('M d, Y') }}</td>
                                    <td><span class="badge {{ $record->status->badgeClass() }}">{{ $record->status->label() }}</span></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
