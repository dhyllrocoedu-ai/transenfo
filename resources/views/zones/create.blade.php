@extends('layouts.app')

@section('title', 'Create Zone')

@section('content')
<div class="card stat-card animate-on-load">
    <div class="card-body">
        <h1 class="h3 mb-4">Create Zone</h1>
        <form method="POST" action="{{ route('zones.store') }}">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Zone Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Assigned Team</label>
                    <select name="team_id" class="form-select">
                        <option value="">Unassigned</option>
                        @foreach ($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Center Latitude</label>
                    <input type="number" step="0.0000001" name="center_latitude" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Center Longitude</label>
                    <input type="number" step="0.0000001" name="center_longitude" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Radius (km)</label>
                    <input type="number" step="0.1" name="radius_km" class="form-control" value="2.5" required>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                        <label class="form-check-label">Active zone</label>
                    </div>
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Zone</button>
                <a href="{{ route('zones.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
