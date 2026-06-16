@extends('layouts.app')

@section('title', 'Clamping Request — '.$request->vehicle_plate)

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/maplibre-gl@latest/dist/maplibre-gl.css">
<style>
    #request-map { width:100%; aspect-ratio:16/9; min-height:300px; border-radius:0.5rem; }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 no-print">
    <div>
        <h1 class="h3 mb-1">Clamping Request</h1>
        <p class="text-muted mb-0">Vehicle: {{ $request->vehicle_plate }}</p>
    </div>
    <div class="d-flex gap-2">
        @can('update', $request)
            @if ($request->status === 'pending')
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">Approve & Assign</button>
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">Reject</button>
            @elseif ($request->status === 'approved' && $request->assigned_to)
                <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#assignModal">Reassign</button>
                <form action="{{ route('clamping-requests.resolve', $request) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-info">Mark Resolved</button>
                </form>
            @elseif ($request->status === 'approved')
                <form action="{{ route('clamping-requests.resolve', $request) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-info">Mark Resolved</button>
                </form>
            @endif
        @endcan
        <a href="{{ route('clamping-requests.index') }}" class="btn btn-outline-secondary">Back</a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card stat-card mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <strong class="text-muted small d-block">Requester Name</strong>
                        {{ $request->requester_name ?? '—' }}
                    </div>
                    <div class="col-md-6">
                        <strong class="text-muted small d-block">Contact</strong>
                        {{ $request->requester_phone ?? '—' }} @if($request->requester_email) <br><small>{{ $request->requester_email }}</small> @endif
                    </div>
                    <div class="col-md-6">
                        <strong class="text-muted small d-block">Vehicle Plate</strong>
                        {{ $request->vehicle_plate }}
                    </div>
                    <div class="col-md-6">
                        <strong class="text-muted small d-block">Date Requested</strong>
                        {{ $request->created_at->format('M d, Y h:i A') }}
                    </div>
                    @if ($request->vehicle_description)
                        <div class="col-12">
                            <strong class="text-muted small d-block">Vehicle Description</strong>
                            {{ $request->vehicle_description }}
                        </div>
                    @endif
                    <div class="col-12">
                        <strong class="text-muted small d-block">Location Address</strong>
                        {{ $request->location_address }}
                    </div>
                    @if ($request->additional_notes)
                        <div class="col-12">
                            <strong class="text-muted small d-block">Additional Notes</strong>
                            {{ $request->additional_notes }}
                        </div>
                    @endif
                    @if ($request->rejection_reason)
                        <div class="col-12">
                            <strong class="text-muted small d-block text-danger">Rejection Reason</strong>
                            {{ $request->rejection_reason }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card stat-card">
            <div class="card-header bg-white"><strong>Location Map</strong></div>
            <div class="card-body p-2">
                <div id="request-map"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card stat-card mb-4">
            <div class="card-header bg-white"><strong>Status</strong></div>
            <div class="card-body">
                <span class="badge bg-{{ $request->getStatusBadgeClass() }} fs-6">{{ $request->getStatusLabel() }}</span>
            </div>
        </div>

        @if ($request->evidence_photo)
            <div class="card stat-card mb-4">
                <div class="card-header bg-white"><strong>Evidence Photo</strong></div>
                <div class="card-body text-center">
                    <img src="{{ Storage::url($request->evidence_photo) }}" alt="Evidence" class="img-fluid rounded" style="max-height:250px">
                </div>
            </div>
        @endif

        @if ($request->processedBy)
            <div class="card stat-card mb-4">
                <div class="card-header bg-white"><strong>Processed By</strong></div>
                <div class="card-body">
                    <p class="mb-1">{{ $request->processedBy->name }}</p>
                    <small class="text-muted">{{ $request->processed_at?->format('M d, Y h:i A') }}</small>
                </div>
            </div>
        @endif

        @if ($request->assignedTo)
            <div class="card stat-card mb-4">
                <div class="card-header bg-white"><strong>Assigned To</strong></div>
                <div class="card-body">
                    <p class="mb-0">{{ $request->assignedTo->name }}</p>
                    @if ($request->assignedTo->email)
                        <small class="text-muted">{{ $request->assignedTo->email }}</small>
                    @endif
                </div>
            </div>
        @endif

        @if ($request->clampingRecord)
            <div class="card stat-card">
                <div class="card-header bg-white"><strong>Related Clamping Record</strong></div>
                <div class="card-body">
                    <p class="mb-1">{{ $request->clampingRecord->notice_number }}</p>
                    <small class="text-muted">{{ $request->clampingRecord->status?->label() }}</small>
                    <br>
                    <a href="{{ route('clamping.show', $request->clampingRecord) }}" class="btn btn-sm btn-outline-primary mt-2">View Record</a>
                </div>
            </div>
        @endif
    </div>
</div>

@can('update', $request)
    @if ($request->status === 'pending')
        <div class="modal fade" id="approveModal" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('clamping-requests.approve', $request) }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header"><h5 class="modal-title">Approve & Assign Request</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body">
                            <p>Assign this clamping request to an enforcer or clamping officer:</p>
                            <select name="assigned_to" class="form-select" required>
                                <option value="">— Select Enforcer —</option>
                                @foreach ($enforcers as $enforcer)
                                    <option value="{{ $enforcer->id }}">{{ $enforcer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Approve & Assign</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="rejectModal" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('clamping-requests.reject', $request) }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header"><h5 class="modal-title">Reject Request</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body">
                            <label class="form-label">Reason for Rejection</label>
                            <textarea name="rejection_reason" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Reject Request</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if ($request->status === 'approved')
        <div class="modal fade" id="assignModal" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('clamping-requests.assign', $request) }}">
                    @csrf
                    <div class="modal-content">
                        <div class="modal-header"><h5 class="modal-title">Reassign Enforcer</h5><button class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body">
                            <select name="assigned_to" class="form-select" required>
                                <option value="">— Select Enforcer —</option>
                                @foreach ($enforcers as $enforcer)
                                    <option value="{{ $enforcer->id }}" @selected($request->assigned_to === $enforcer->id)>{{ $enforcer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Assign</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endcan
@endsection

@push('scripts')
<script src="https://unpkg.com/maplibre-gl@latest/dist/maplibre-gl.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lat = {{ $request->latitude }};
        const lng = {{ $request->longitude }};

        const map = new maplibregl.Map({
            container: 'request-map',
            style: 'https://basemaps.cartocdn.com/gl/positron-gl-style/style.json',
            center: [lng, lat],
            zoom: 15,
        });

        map.on('load', function () {
            map.addSource('pin', {
                type: 'geojson',
                data: {
                    type: 'FeatureCollection',
                    features: [{
                        type: 'Feature',
                        geometry: { type: 'Point', coordinates: [lng, lat] },
                        properties: { description: '{{ addslashes($request->location_address) }}' },
                    }],
                },
            });

            map.addLayer({
                id: 'pin-layer',
                type: 'circle',
                source: 'pin',
                paint: {
                    'circle-radius': 10,
                    'circle-color': '#dc3545',
                    'circle-stroke-width': 3,
                    'circle-stroke-color': '#fff',
                },
            });

            new maplibregl.Popup({ closeOnClick: true })
                .setLngLat([lng, lat])
                .setHTML('<strong>Requested Location</strong><br>{{ addslashes($request->location_address) }}')
                .addTo(map);
        });
    });
</script>
@endpush
