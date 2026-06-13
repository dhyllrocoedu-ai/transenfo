@extends('layouts.app')

@section('title', 'Vehicle Releases')

@section('content')
<h1 class="h3 mb-4">Vehicle Releases</h1>

@if ($activeClamps->isNotEmpty())
    <div class="card stat-card mb-4">
        <div class="card-header bg-white"><strong>Active Clamps Pending Release</strong></div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead><tr><th>Notice #</th><th>Vehicle</th><th>Clamped At</th><th></th></tr></thead>
                <tbody>
                    @foreach ($activeClamps as $clamp)
                        <tr>
                            <td>{{ $clamp->notice_number }}</td>
                            <td>{{ $clamp->vehicle->plate_number }}</td>
                            <td>{{ $clamp->clamped_at->format('M d, Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('releases.create', $clamp) }}" class="btn btn-sm btn-success">Release</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<div class="card stat-card">
    <div class="card-header bg-white"><strong>Release History</strong></div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead><tr><th>Release #</th><th>Vehicle</th><th>Released By</th><th>Date</th></tr></thead>
            <tbody>
                @forelse ($releases as $release)
                    <tr>
                        <td>{{ $release->release_number }}</td>
                        <td>{{ $release->clampingRecord->vehicle->plate_number }}</td>
                        <td>{{ $release->releasedByUser->name }}</td>
                        <td>{{ $release->released_at->format('M d, Y') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted py-4">No releases recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($releases->hasPages())<div class="card-footer bg-white">{{ $releases->links() }}</div>@endif
</div>
@endsection
