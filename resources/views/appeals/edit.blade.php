@extends('layouts.app')

@section('title', 'Review Appeal')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Review Appeal</h1>
        <p class="text-muted mb-0">Update the appeal decision and record review notes.</p>
    </div>
</div>

<div class="card stat-card">
    <div class="card-body">
        <form method="POST" action="{{ route('appeals.update', $appeal) }}">
            @csrf
            @method('PATCH')
            <div class="mb-3">
                <label class="form-label">Decision</label>
                <select name="status" class="form-select" required>
                    <option value="under_review" @selected($appeal->status->value === 'under_review')>Under Review</option>
                    <option value="approved" @selected($appeal->status->value === 'approved')>Approved</option>
                    <option value="rejected" @selected($appeal->status->value === 'rejected')>Rejected</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Decision Notes</label>
                <textarea name="decision_notes" rows="4" class="form-control">{{ old('decision_notes', $appeal->decision_notes) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Review</button>
            <a href="{{ route('appeals.show', $appeal) }}" class="btn btn-link">Cancel</a>
        </form>
    </div>
</div>
@endsection
