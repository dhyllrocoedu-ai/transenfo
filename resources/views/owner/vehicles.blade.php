@extends('layouts.app')

@section('title', 'My Vehicles')

@section('content')
<h1 class="h3 mb-4">My Vehicles</h1>
<div class="card stat-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Plate #</th><th>Classification</th><th>Driver</th><th>Citations</th><th>Status</th><th></th></tr></thead>
            <tbody>
                @forelse ($vehicles as $vehicle)
                    <tr>
                        <td>{{ $vehicle->plate_number }}</td>
                        <td>{{ $vehicle->classification }}</td>
                        <td>{{ $vehicle->driver?->fullName() ?? '—' }}</td>
                        <td>{{ $vehicle->citations->count() }}</td>
                        <td>{{ ucfirst($vehicle->registration_status) }}</td>
                        <td class="text-end"><a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-4">No vehicles registered to your account.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($vehicles->hasPages())<div class="card-footer bg-white">{{ $vehicles->links() }}</div>@endif
</div>
@endsection
