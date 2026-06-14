@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <!-- HEADER -->
            <div class="mb-4 animate-on-load">
                <h2 class="fw-bold mb-2">
                    <i class="bi bi-search me-2"></i>Citation Lookup
                </h2>
                <p class="text-muted mb-0">Find your citation and check its status instantly</p>
            </div>

            <!-- ERROR MESSAGE -->
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show animate-on-load" role="alert">
                    <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- SEARCH CARD -->
            <div class="card stat-card mb-4 animate-on-load">
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted fw-semibold d-block mb-2">SEARCH TYPE</small>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="search_type" id="search_type_simple" value="simple" checked>
                            <label class="btn btn-outline-primary" for="search_type_simple">
                                <i class="bi bi-lightning me-1"></i>Quick Search
                            </label>
                            <input type="radio" class="btn-check" name="search_type" id="search_type_advanced" value="advanced">
                            <label class="btn btn-outline-primary" for="search_type_advanced">
                                <i class="bi bi-sliders me-1"></i>Advanced
                            </label>
                        </div>
                    </div>

                    <form action="{{ route('citizen.citation.search') }}" method="GET" id="searchForm">
                        <!-- SIMPLE SEARCH -->
                        <div id="simple-search" class="mb-4">
                            <label for="search" class="form-label fw-semibold">
                                <i class="bi bi-receipt me-1"></i>Citation or Plate Number
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="search"
                                    name="search"
                                    placeholder="e.g., CIT-2024-001 or ABC-1234"
                                    value="{{ old('search') }}"
                                    autocomplete="off"
                                >
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="bi bi-info-circle me-1"></i>Minimum 3 characters required
                            </small>
                            @error('search')
                                <small class="text-danger d-block mt-1">{{ $message }}</small>
                            @enderror
                        </div>

                        <!-- ADVANCED SEARCH (HIDDEN BY DEFAULT) -->
                        <div id="advanced-search" style="display: none;" class="mb-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="adv_citation" class="form-label">Citation Number</label>
                                    <input type="text" class="form-control" id="adv_citation" name="citation_number" placeholder="e.g., CIT-2024-001">
                                </div>
                                <div class="col-12">
                                    <label for="adv_plate" class="form-label">Vehicle Plate</label>
                                    <input type="text" class="form-control" id="adv_plate" name="plate_number" placeholder="e.g., ABC-1234" style="text-transform: uppercase;">
                                </div>
                                <div class="col-12">
                                    <label for="adv_date" class="form-label">Issued Date</label>
                                    <input type="date" class="form-control" id="adv_date" name="issued_date">
                                </div>
                                <div class="col-12">
                                    <label for="adv_status" class="form-label">Status</label>
                                    <select class="form-select" id="adv_status" name="status">
                                        <option value="">All Statuses</option>
                                        <option value="issued">Issued</option>
                                        <option value="paid">Paid</option>
                                        <option value="overdue">Overdue</option>
                                        <option value="appealed">Appealed</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg fw-semibold">
                                <i class="bi bi-search me-2"></i>Search Citation
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- QUICK INFO CARDS -->
            <div class="row g-2 mb-4">
                <div class="col-6 animate-on-load" style="animation-delay: 0.1s;">
                    <a href="{{ route('citizen.clamping.show') }}" class="text-decoration-none">
                        <div class="card stat-card h-100 text-center">
                            <div class="card-body">
                                <i class="bi bi-shield-exclamation text-warning fs-4 d-block mb-2"></i>
                                <small class="fw-semibold d-block">Report Parking</small>
                                <small class="text-muted">Request vehicle clamping</small>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-6 animate-on-load" style="animation-delay: 0.2s;">
                    <a href="#" class="text-decoration-none" onclick="alert('Payment portal coming soon!')">
                        <div class="card stat-card h-100 text-center">
                            <div class="card-body">
                                <i class="bi bi-credit-card text-success fs-4 d-block mb-2"></i>
                                <small class="fw-semibold d-block">Pay Citation</small>
                                <small class="text-muted">Online payment portal</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- INFORMATION SECTION -->
            <div class="card stat-card animate-on-load" style="animation-delay: 0.3s;">
                <div class="card-body">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="bi bi-shield-check me-2"></i>What's Available
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <div style="width: 2rem; height: 2rem; background: rgba(37,99,235,0.12); border-radius: 0.6rem; display: grid; place-items: center; color: #2563eb; flex-shrink: 0;">
                                    <i class="bi bi-eye"></i>
                                </div>
                                <div>
                                    <strong class="d-block">View Details</strong>
                                    <small class="text-muted">Violation, date, location, amount</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <div style="width: 2rem; height: 2rem; background: rgba(37,99,235,0.12); border-radius: 0.6rem; display: grid; place-items: center; color: #2563eb; flex-shrink: 0;">
                                    <i class="bi bi-images"></i>
                                </div>
                                <div>
                                    <strong class="d-block">Evidence Photos</strong>
                                    <small class="text-muted">View violation evidence</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <div style="width: 2rem; height: 2rem; background: rgba(37,99,235,0.12); border-radius: 0.6rem; display: grid; place-items: center; color: #2563eb; flex-shrink: 0;">
                                    <i class="bi bi-qr-code"></i>
                                </div>
                                <div>
                                    <strong class="d-block">QR Code</strong>
                                    <small class="text-muted">Printable reference code</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex gap-2">
                                <div style="width: 2rem; height: 2rem; background: rgba(37,99,235,0.12); border-radius: 0.6rem; display: grid; place-items: center; color: #2563eb; flex-shrink: 0;">
                                    <i class="bi bi-cash"></i>
                                </div>
                                <div>
                                    <strong class="d-block">Payment Status</strong>
                                    <small class="text-muted">Track payment history</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const simpleRadio = document.getElementById('search_type_simple');
    const advancedRadio = document.getElementById('search_type_advanced');
    const simpleSearch = document.getElementById('simple-search');
    const advancedSearch = document.getElementById('advanced-search');
    const searchForm = document.getElementById('searchForm');

    simpleRadio.addEventListener('change', function() {
        simpleSearch.style.display = 'block';
        advancedSearch.style.display = 'none';
        document.getElementById('search').required = true;
    });

    advancedRadio.addEventListener('change', function() {
        simpleSearch.style.display = 'none';
        advancedSearch.style.display = 'block';
        document.getElementById('search').required = false;
    });

    // Add animation on load
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.animate-on-load').forEach(el => observer.observe(el));
});
</script>

@endsection

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-search me-2"></i>Citation Lookup
                    </h4>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted mb-4">
                        Enter your citation number or vehicle plate number to check the status of your traffic citation.
                    </p>

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form action="{{ route('citizen.citation.search') }}" method="GET">
                        <div class="mb-3">
                            <label for="search" class="form-label fw-semibold">
                                Citation Number or Plate Number
                            </label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-receipt"></i></span>
                                <input
                                    type="text"
                                    class="form-control form-control-lg"
                                    id="search"
                                    name="search"
                                    placeholder="e.g. CIT-2024-000123 or ABC-1234"
                                    value="{{ old('search') }}"
                                    required
                                    autocomplete="off"
                                >
                            </div>
                            <div class="form-text">
                                Minimum 3 characters. Search by citation number (e.g., CIT-2024-000123) or vehicle plate (e.g., ABC-1234).
                            </div>
                            @error('search')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-search me-2"></i>Search Citation
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center text-muted small">
                        <p class="mb-1">
                            <i class="bi bi-info-circle me-1"></i>
                            Have questions about your citation?
                        </p>
                        <a href="#" class="text-decoration-none">Contact Traffic Enforcement Office</a>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">
                        <i class="bi bi-shield-check me-2"></i>What You Can Do
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-eye text-primary fs-4 me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">View Citation Details</h6>
                                    <p class="text-muted small mb-0">See violation type, location, date, and penalty amount</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-images text-primary fs-4 me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">View Evidence</h6>
                                    <p class="text-muted small mb-0">Access photos/videos attached to the citation</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-cash-stack text-primary fs-4 me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">Check Payment Status</h6>
                                    <p class="text-muted small mb-0">See if payment has been made or is still pending</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-chat-square-text text-primary fs-4 me-3 mt-1"></i>
                                <div>
                                    <h6 class="mb-1">File an Appeal</h6>
                                    <p class="text-muted small mb-0">Submit an appeal if you believe the citation was issued in error</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection