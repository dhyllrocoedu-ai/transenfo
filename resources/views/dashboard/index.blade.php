@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Dashboard</h1>
        <p class="text-muted mb-0">Enforcement operations overview</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted small">Total Citations</div>
                <div class="h3 mb-0">{{ number_format($stats['total_citations']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted small">Unpaid Citations</div>
                <div class="h3 mb-0 text-warning">{{ number_format($stats['unpaid_citations']) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted small">Payments Today</div>
                <div class="h3 mb-0 text-success">₱{{ number_format($stats['payments_today'], 2) }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body">
                <div class="text-muted small">Active Clamps</div>
                <div class="h3 mb-0 text-danger">{{ number_format($stats['active_clamps']) }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card stat-card h-100">
            <div class="card-header bg-white"><strong>Citations by Month</strong></div>
            <div class="card-body">
                <canvas id="citationsChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card stat-card h-100">
            <div class="card-header bg-white"><strong>Payments Collected</strong></div>
            <div class="card-body">
                <canvas id="paymentsChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const citationLabels = @json($citationsByMonth->keys()->values());
    const citationData = @json($citationsByMonth->values());
    const paymentLabels = @json($paymentsByMonth->keys()->values());
    const paymentData = @json($paymentsByMonth->values());

    new Chart(document.getElementById('citationsChart'), {
        type: 'bar',
        data: {
            labels: citationLabels,
            datasets: [{ label: 'Citations', data: citationData, backgroundColor: '#2563eb' }]
        },
        options: { responsive: true, plugins: { legend: { display: false } } }
    });

    new Chart(document.getElementById('paymentsChart'), {
        type: 'line',
        data: {
            labels: paymentLabels,
            datasets: [{ label: 'Amount (₱)', data: paymentData, borderColor: '#16a34a', fill: false }]
        },
        options: { responsive: true }
    });
});
</script>
@endpush
