@extends('layouts.app')

@section('title', 'Issue Citation')

@section('content')
<h1 class="h3 mb-4">Issue Citation</h1>
<div class="card stat-card"><div class="card-body">
    <form method="POST" action="{{ route('citations.store') }}" enctype="multipart/form-data">@csrf
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Vehicle</label>
                <select name="vehicle_id" class="form-select" required>
                    <option value="">Select vehicle...</option>
                    @foreach ($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}" @selected(old('vehicle_id', $selectedVehicle?->id) == $vehicle->id)>
                            {{ $vehicle->plate_number }} — {{ $vehicle->classification }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Driver (optional)</label>
                <select name="driver_id" class="form-select">
                    <option value="">— None —</option>
                    @foreach ($drivers as $driver)
                        <option value="{{ $driver->id }}" @selected(old('driver_id') == $driver->id)>{{ $driver->fullName() }} ({{ $driver->license_number }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Violation Type</label>
                <select name="violation_type_id" class="form-select" required>
                    <option value="">Select violation...</option>
                    @foreach ($violationTypes as $type)
                        <option value="{{ $type->id }}" @selected(old('violation_type_id') == $type->id)>
                            {{ $type->name }} — ₱{{ number_format($type->penalty_amount, 2) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Location</label>
                <input type="text" name="location" class="form-control" value="{{ old('location') }}" placeholder="Violation location">
            </div>
            <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
            </div>
            <div class="col-12">
                <label class="form-label">Evidence Photos</label>
                <input type="file" name="evidence[]" class="form-control" accept="image/*" multiple>
            </div>
        </div>
        <div class="mt-4">
            <button type="submit" class="btn btn-danger">Issue Citation</button>
            <a href="{{ route('citations.index') }}" class="btn btn-link">Cancel</a>
        </div>
    </form>
</div></div>
@endsection
