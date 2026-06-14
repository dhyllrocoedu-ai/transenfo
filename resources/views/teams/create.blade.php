@extends('layouts.app')

@section('title', 'Create Team')

@section('content')
<div class="card stat-card animate-on-load">
    <div class="card-body">
        <h1 class="h3 mb-4">Create Team</h1>
        <form method="POST" action="{{ route('teams.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Team Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Team Lead</label>
                    <select name="leader_id" class="form-select">
                        <option value="">Select a lead</option>
                        @foreach ($leaders as $leader)
                            <option value="{{ $leader->id }}">{{ $leader->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Members</label>
                    <select name="members[]" class="form-select" multiple size="8">
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}">{{ $member->name }} ({{ $member->role->label() }})</option>
                        @endforeach
                    </select>
                    <div class="form-text">Hold Ctrl/Cmd to select multiple members.</div>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <label class="form-check-label">Active team</label>
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Team</button>
                <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
