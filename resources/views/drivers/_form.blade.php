@php($driver = $driver ?? null)
<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">First Name</label>
        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $driver?->first_name) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">Last Name</label>
        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $driver?->last_name) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">License Number</label>
        <input type="text" name="license_number" class="form-control" value="{{ old('license_number', $driver?->license_number) }}" required>
    </div>
    <div class="col-md-6">
        <label class="form-label">License Expiry</label>
        <input type="date" name="license_expiry" class="form-control" value="{{ old('license_expiry', $driver?->license_expiry?->format('Y-m-d')) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Phone</label>
        <input type="text" name="phone" class="form-control" value="{{ old('phone', $driver?->phone) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $driver?->email) }}">
    </div>
    <div class="col-12">
        <label class="form-label">Address</label>
        <textarea name="address" class="form-control" rows="2">{{ old('address', $driver?->address) }}</textarea>
    </div>
</div>
