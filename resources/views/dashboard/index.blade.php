@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4 animate-on-load">
    <div>
        <h1 class="h3 mb-1">Dashboard</h1>
        <p class="text-muted mb-0">Enforcement operations overview for {{ auth()->user()->name }}</p>
    </div>
    <div class="text-muted small">{{ auth()->user()->role->label() }} • {{ now()->format('F j, Y') }}</div>
</div>

<div class="row g-3 mb-4">
    @foreach ([
        ['label' => 'Total Citations', 'value' => number_format($stats['total_citations']), 'tone' => 'primary'],
        ['label' => 'Unpaid Citations', 'value' => number_format($stats['unpaid_citations']), 'tone' => 'warning'],
        ['label' => 'Payments Today', 'value' => '₱'.number_format($stats['payments_today'], 2), 'tone' => 'success'],
        ['label' => 'Active Clamps', 'value' => number_format($stats['active_clamps']), 'tone' => 'danger'],
    ] as $stat)
        <div class="col-md-6 col-xl-3">
            <div class="card stat-card border-0 h-100 animate-on-load">
                <div class="card-body">
                    <div class="text-muted small">{{ $stat['label'] }}</div>
                    <div class="h3 mb-0 text-{{ $stat['tone'] }}">{{ $stat['value'] }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-xl-8">
        <div class="card stat-card h-100 animate-on-load">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <strong>Recent activity</strong>
                    <p class="text-muted small mb-0">Latest enforcement events across the office</p>
                </div>
            </div>
            <div class="card-body">
                @forelse ($recentActivity as $item)
                    <div class="d-flex align-items-start justify-content-between gap-3 border-bottom py-3">
                        <div class="d-flex gap-3">
                            <div class="activity-icon"><i class="bi {{ $item['icon'] }}"></i></div>
                            <div>
                                <div class="fw-semibold">{{ $item['title'] }}</div>
                                <div class="text-muted small">{{ $item['description'] }}</div>
                            </div>
                        </div>
                        <div class="text-muted small text-nowrap">{{ $item['timestamp_label'] }}</div>
                    </div>
                @empty
                    <div class="text-muted text-center py-4">No recent activity yet.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card stat-card h-100 animate-on-load">
            <div class="card-header bg-white">
                <strong>Quick actions</strong>
            </div>
            <div class="card-body d-grid gap-2">
                <a href="{{ route('citations.create') }}" class="btn btn-outline-primary text-start"><i class="bi bi-receipt me-2"></i>Issue Citation</a>
                <a href="{{ route('payments.create') }}" class="btn btn-outline-success text-start"><i class="bi bi-cash-stack me-2"></i>Process Payment</a>
                <a href="{{ route('clamping.create') }}" class="btn btn-outline-danger text-start"><i class="bi bi-lock me-2"></i>Record Clamp</a>
                <a href="{{ route('appeals.index') }}" class="btn btn-outline-secondary text-start"><i class="bi bi-chat-square-text me-2"></i>Review Appeals</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-lg-6">
        <div class="card stat-card h-100 animate-on-load">
            <div class="card-header bg-white"><strong>Citations by Month</strong></div>
            <div class="card-body">
                <canvas id="citationsChart" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card stat-card h-100 animate-on-load">
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
