@php($vehicle = $vehicle ?? null)
<div class="row g-3">
    <div class="col-md-4">
        <label class="form-label">Plate Number</label>
        <input type="text" name="plate_number" class="form-control text-uppercase" value="{{ old('plate_number', $vehicle?->plate_number) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Classification</label>
        <input type="text" name="classification" class="form-control" value="{{ old('classification', $vehicle?->classification) }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Registration Status</label>
        <select name="registration_status" class="form-select" required>
            @foreach (['active', 'suspended', 'expired'] as $status)
                <option value="{{ $status }}" @selected(old('registration_status', $vehicle?->registration_status) === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Make</label>
        <input type="text" name="make" class="form-control" value="{{ old('make', $vehicle?->make) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Model</label>
        <input type="text" name="model" class="form-control" value="{{ old('model', $vehicle?->model) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Year</label>
        <input type="number" name="year" class="form-control" value="{{ old('year', $vehicle?->year) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Color</label>
        <input type="text" name="color" class="form-control" value="{{ old('color', $vehicle?->color) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label">Owner</label>
        <select name="owner_id" class="form-select">
            <option value="">— None —</option>
            @foreach ($owners as $owner)
                <option value="{{ $owner->id }}" @selected(old('owner_id', $vehicle?->owner_id) == $owner->id)>{{ $owner->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label">Driver</label>
        <select name="driver_id" class="form-select">
            <option value="">— None —</option>
            @foreach ($drivers as $driver)
                <option value="{{ $driver->id }}" @selected(old('driver_id', $vehicle?->driver_id) == $driver->id)>{{ $driver->fullName() }}</option>
            @endforeach
        </select>
    </div>
</div>
