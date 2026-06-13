@extends('layouts.app')

@section('title', 'Process Release')

@section('content')
<h1 class="h3 mb-4">Process Vehicle Release</h1>

<div class="card stat-card mb-4">
    <div class="card-body">
        <p><strong>Clamp Notice:</strong> {{ $clamping->notice_number }}</p>
        <p><strong>Vehicle:</strong> {{ $clamping->vehicle->plate_number }}</p>
        <p class="mb-0"><strong>Clamped:</strong> {{ $clamping->clamped_at->format('M d, Y h:i A') }}</p>
    </div>
</div>

@if ($unpaidCitations->isNotEmpty())
    <div class="alert alert-danger">
        <strong>Cannot release:</strong> {{ $unpaidCitations->count() }} unpaid citation(s) remain.
        <ul class="mb-0 mt-2">
            @foreach ($unpaidCitations as $citation)
                <li>{{ $citation->citation_number }} — ₱{{ number_format($citation->penalty_amount, 2) }} ({{ $citation->status->label() }})</li>
            @endforeach
        </ul>
    </div>
@else
    <div class="alert alert-success">All citations are paid. Vehicle is eligible for release.</div>
    <div class="card stat-card"><div class="card-body">
        <form method="POST" action="{{ route('releases.store', $clamping) }}">@csrf
            <div class="mb-3">
                <label class="form-label">Release Notes</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
            </div>
            <button type="submit" class="btn btn-success">Confirm Release</button>
            <a href="{{ route('releases.index') }}" class="btn btn-link">Cancel</a>
        </form>
    </div></div>
@endif
@endsection
