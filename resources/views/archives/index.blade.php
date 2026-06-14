@extends('layouts.app')

@section('title', 'Archives')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Archives</h1>
</div>

<div class="card stat-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead><tr><th>Type</th><th>ID</th><th>Archived By</th><th>Date</th><th>Reason</th></tr></thead>
            <tbody>
                @forelse ($archives as $archive)
                    <tr>
                        <td>{{ class_basename($archive->archivable_type) }}</td>
                        <td>#{{ $archive->archivable_id }}</td>
                        <td>{{ $archive->archivedBy?->name ?? 'System' }}</td>
                        <td>{{ $archive->archived_at->format('M d, Y H:i') }}</td>
                        <td class="small text-muted">{{ $archive->reason ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-4">No archived records.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($archives->hasPages())<div class="card-footer bg-white">{{ $archives->links() }}</div>@endif
</div>
@endsection
