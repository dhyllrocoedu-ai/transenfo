@extends('layouts.app')

@section('title', 'Clamping Requests')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Clamping Requests</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('clamping-requests.index') }}" class="btn btn-outline-secondary btn-sm @if(!request('status')) active @endif">All</a>
        <a href="{{ route('clamping-requests.index', ['status' => 'pending']) }}" class="btn btn-outline-warning btn-sm @if(request('status') === 'pending') active @endif">Pending</a>
        <a href="{{ route('clamping-requests.index', ['status' => 'approved']) }}" class="btn btn-outline-success btn-sm @if(request('status') === 'approved') active @endif">Approved</a>
        <a href="{{ route('clamping-requests.index', ['status' => 'rejected']) }}" class="btn btn-outline-danger btn-sm @if(request('status') === 'rejected') active @endif">Rejected</a>
        <a href="{{ route('clamping-requests.index', ['status' => 'resolved']) }}" class="btn btn-outline-info btn-sm @if(request('status') === 'resolved') active @endif">Resolved</a>
    </div>
</div>

<div class="card stat-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Requester</th>
                    <th>Vehicle Plate</th>
                    <th>Location</th>
                    <th>Assigned To</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($requests as $r)
                    <tr>
                        <td>{{ $r->requester_name ?? '—' }}</td>
                        <td>{{ $r->vehicle_plate }}</td>
                        <td class="small text-muted" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $r->location_address }}</td>
                        <td>{{ $r->assignedTo?->name ?? '—' }}</td>
                        <td>{{ $r->created_at->format('M d, Y') }}</td>
                        <td><span class="badge bg-{{ $r->getStatusBadgeClass() }}">{{ $r->getStatusLabel() }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('clamping-requests.show', $r) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">No clamping requests found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($requests->hasPages())<div class="card-footer bg-white">{{ $requests->links() }}</div>@endif
</div>
@endsection
