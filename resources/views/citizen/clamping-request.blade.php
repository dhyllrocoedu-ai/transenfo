@extends('layouts.guest')

@section('title', 'Report Illegal Parking')

@section('content')
<div class="container py-5" style="max-width: 700px;">
    <div class="d-flex align-items-center gap-3 mb-4 animate-on-load">
        <a href="{{ route('citizen.citation.lookup') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
        <div>
            <h2 class="mb-0">Report Illegally Parked Vehicle</h2>
            <p class="text-muted mb-0">Help us enforce parking regulations in your area</p>
        </div>
    </div>

    <div class="card stat-card animate-on-load">
        <div class="card-body">
            <form method="POST" action="{{ route('citizen.clamping.store') }}" enctype="multipart/form-data">
                @csrf
                
                <!-- REQUESTER SECTION -->
                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-fill me-2"></i>Your Information</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Full Name *</label>
                        <input type="text" name="requester_name" class="form-control @error('requester_name') is-invalid @enderror" value="{{ old('requester_name') }}" required>
                        @error('requester_name')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Phone Number *</label>
                        <input type="tel" name="requester_phone" class="form-control @error('requester_phone') is-invalid @enderror" value="{{ old('requester_phone') }}" required>
                        @error('requester_phone')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Email Address *</label>
                        <input type="email" name="requester_email" class="form-control @error('requester_email') is-invalid @enderror" value="{{ old('requester_email') }}" required>
                        @error('requester_email')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                    </div>
                </div>

                <!-- LOCATION SECTION -->
                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-geo-alt-fill me-2"></i>Location Details</h6>
                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Address/Location *</label>
                        <input type="text" name="location_address" class="form-control @error('location_address') is-invalid @enderror" placeholder="e.g., 123 Main St, Barangay Marikina" value="{{ old('location_address') }}" required>
                        @error('location_address')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Latitude *</label>
                        <div class="input-group">
                            <input type="number" step="0.000001" name="latitude" id="latitude" class="form-control @error('latitude') is-invalid @enderror" value="{{ old('latitude') }}" required readonly>
                            <button type="button" class="btn btn-outline-primary" id="gpsButton" onclick="getGPSCoordinates()">
                                <i class="bi bi-crosshair"></i> Get GPS
                            </button>
                        </div>
                        @error('latitude')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Longitude *</label>
                        <input type="number" step="0.000001" name="longitude" id="longitude" class="form-control @error('longitude') is-invalid @enderror" value="{{ old('longitude') }}" required readonly>
                        @error('longitude')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                    </div>
                </div>

                <!-- VEHICLE SECTION -->
                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-car-front-fill me-2"></i>Vehicle Information</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">License Plate *</label>
                        <input type="text" name="vehicle_plate" class="form-control text-uppercase @error('vehicle_plate') is-invalid @enderror" placeholder="e.g., ABC 1234" value="{{ old('vehicle_plate') }}" required>
                        @error('vehicle_plate')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Vehicle Description</label>
                        <input type="text" name="vehicle_description" class="form-control" placeholder="e.g., White Toyota Corolla" value="{{ old('vehicle_description') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Additional Notes</label>
                        <textarea name="additional_notes" class="form-control" rows="3" placeholder="Any additional details...">{{ old('additional_notes') }}</textarea>
                    </div>
                </div>

                <!-- EVIDENCE SECTION -->
                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-camera-fill me-2"></i>Evidence</h6>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Photo of Vehicle *</label>
                    <div class="position-relative">
                        <input type="file" name="evidence_photo" class="form-control @error('evidence_photo') is-invalid @enderror" accept="image/*" required id="photoInput" onchange="previewPhoto(event)">
                        <small class="text-muted d-block mt-2"><i class="bi bi-info-circle me-1"></i>Maximum 5MB. Clear photo showing license plate and parking violation.</small>
                    </div>
                    @error('evidence_photo')<small class="text-danger d-block mt-1">{{ $message }}</small>@enderror
                    <div id="photoPreview" class="mt-3"></div>
                </div>

                <!-- SUBMISSION -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1 fw-semibold">
                        <i class="bi bi-send me-2"></i>Submit Request
                    </button>
                    <a href="{{ route('citizen.citation.lookup') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>

                <p class="text-muted small mt-3 mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    By submitting this request, you confirm that the information is accurate and the vehicle is illegally parked on your property.
                </p>
            </form>
        </div>
    </div>
</div>

<script>
function getGPSCoordinates() {
    const button = document.getElementById('gpsButton');
    button.disabled = true;
    button.innerHTML = '<i class="bi bi-hourglass-split"></i> Getting location...';
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitude').value = position.coords.latitude.toFixed(6);
                document.getElementById('longitude').value = position.coords.longitude.toFixed(6);
                button.disabled = false;
                button.innerHTML = '<i class="bi bi-check-circle"></i> Location Set';
                button.classList.add('btn-success');
                button.classList.remove('btn-outline-primary');
            },
            function(error) {
                alert('Unable to get GPS coordinates: ' + error.message);
                button.disabled = false;
                button.innerHTML = '<i class="bi bi-crosshair"></i> Get GPS';
            }
        );
    } else {
        alert('Geolocation is not supported by your browser.');
        button.disabled = false;
    }
}

function previewPhoto(event) {
    const preview = document.getElementById('photoPreview');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <div style="position: relative; display: inline-block;">
                    <img src="${e.target.result}" style="max-width: 250px; border-radius: 0.5rem; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    <small class="text-muted d-block mt-2"><i class="bi bi-check-circle text-success me-1"></i>Photo selected</small>
                </div>
            `;
        };
        reader.readAsDataURL(file);
    }
}
</script>

@endsection
