@extends('layouts.app')

@section('title', 'GPS Tracking')

@push('scripts')
@vite(['resources/js/tracking.js'])
<script>
document.addEventListener('DOMContentLoaded', () => {
    const { initTrackingMap } = window.__tracking;
    if (initTrackingMap) {
        initTrackingMap({
            containerId: 'tracking-map',
            enforcersEndpoint: '{{ route("tracking.locations") }}',
            styleUrl: 'https://tiles.openfreemap.org/styles/liberty',
            center: [121.0402, 14.5432],
            zoom: 12,
            refreshInterval: 15000,
        });
    }
});
</script>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 animate-on-load">
    <div>
        <h1 class="h3 mb-1">GPS Tracking</h1>
        <p class="text-muted mb-0">Real-time enforcer location monitoring with interactive map.</p>
    </div>
    <div class="d-flex gap-2">
        <button id="toggle-3d" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-box me-1"></i>3D View
        </button>
        <span class="text-muted small align-self-center">
            <span id="enforcer-count">0</span> active
        </span>
    </div>
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <div class="card stat-card h-100 animate-on-load p-0 overflow-hidden">
            <div id="tracking-map" class="tracking-map-container" style="min-height:600px;"></div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card stat-card animate-on-load">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong><i class="bi bi-person-badge me-1"></i>Enforcer Detail</strong>
            </div>
            <div class="card-body" id="enforcer-detail">
                <div class="text-center text-muted py-5">
                    <i class="bi bi-geo-alt fs-1 d-block mb-2"></i>
                    <p class="mb-0">Select an enforcer on the map to view details.</p>
                </div>
            </div>
        </div>

        <div class="card stat-card mt-3 animate-on-load">
            <div class="card-header bg-white">
                <strong><i class="bi bi-people me-1"></i>All Enforcers</strong>
            </div>
            <div class="card-body p-0 enforcer-panel" id="enforcer-list">
                <div class="text-center text-muted py-4">
                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                    Loading...
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
