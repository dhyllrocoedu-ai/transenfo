@extends('layouts.app')

@section('title', 'Edit Team')

@section('content')
<div class="card stat-card animate-on-load">
    <div class="card-body">
        <h1 class="h3 mb-4">Edit Team</h1>
        <form method="POST" action="{{ route('teams.update', $team) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Team Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $team->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Team Lead</label>
                    <select name="leader_id" class="form-select">
                        <option value="">Select a lead</option>
                        @foreach ($leaders as $leader)
                            <option value="{{ $leader->id }}" @selected(old('leader_id', $team->leader_id) == $leader->id)>{{ $leader->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $team->description) }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Members</label>
                    <select name="members[]" class="form-select" multiple size="8">
                        @foreach ($members as $member)
                            <option value="{{ $member->id }}" @selected($team->members->contains($member->id))>{{ $member->name }} ({{ $member->role->label() }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old('is_active', $team->is_active))>
                        <label class="form-check-label">Active team</label>
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Team</button>
                <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
