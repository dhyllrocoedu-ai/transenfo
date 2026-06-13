<?php

namespace App\Http\Controllers;

use App\Enums\CitationStatus;
use App\Enums\ClampingStatus;
use App\Http\Requests\StoreClampingRequest;
use App\Models\ClampingRecord;
use App\Models\Vehicle;
use App\Services\CitationNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClampingController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', ClampingRecord::class);

        $query = ClampingRecord::with(['vehicle', 'officer', 'citation']);

        if (! auth()->user()->isStaff()) {
            $query->whereHas('vehicle', fn ($q) => $q->where('owner_id', auth()->id()));
        }

        $records = $query->latest('clamped_at')->paginate(15);

        $eligibleDays = config('itevcms.clamping_eligible_days');
        $eligibleVehicles = collect();

        if (auth()->user()->isRole(\App\Enums\Role::SuperAdmin, \App\Enums\Role::Administrator, \App\Enums\Role::ClampingOfficer)) {
            $eligibleVehicles = Vehicle::query()
                ->whereDoesntHave('clampingRecords', fn ($q) => $q->where('status', ClampingStatus::Active))
                ->whereHas('citations', function ($q) use ($eligibleDays) {
                    $q->whereIn('status', [CitationStatus::Overdue, CitationStatus::Issued])
                        ->whereDate('due_date', '<=', now()->subDays($eligibleDays));
                })
                ->with(['citations' => fn ($q) => $q->whereIn('status', [CitationStatus::Overdue, CitationStatus::Issued])])
                ->orderBy('plate_number')
                ->get();
        }

        return view('clamping.index', compact('records', 'eligibleVehicles'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', ClampingRecord::class);

        $vehicle = $request->vehicle_id ? Vehicle::with('citations')->find($request->vehicle_id) : null;

        return view('clamping.create', compact('vehicle'));
    }

    public function store(StoreClampingRequest $request, CitationNumberService $numberService): RedirectResponse
    {
        $this->authorize('create', ClampingRecord::class);

        $vehicle = Vehicle::findOrFail($request->vehicle_id);

        if ($vehicle->activeClamp()) {
            return back()->withErrors(['vehicle_id' => 'This vehicle is already clamped.']);
        }

        $citation = $vehicle->citations()
            ->whereIn('status', [CitationStatus::Issued, CitationStatus::Overdue])
            ->latest('issued_at')
            ->first();

        $evidencePath = null;
        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('clamping', 'public');
        }

        $record = ClampingRecord::create([
            'notice_number' => $numberService->noticeNumber(),
            'vehicle_id' => $vehicle->id,
            'citation_id' => $citation?->id,
            'clamped_by' => auth()->id(),
            'status' => ClampingStatus::Active,
            'location' => $request->location,
            'notes' => $request->notes,
            'evidence_path' => $evidencePath,
            'clamped_at' => now(),
        ]);

        if ($citation) {
            $citation->update(['status' => CitationStatus::Clamped]);
        }

        return redirect()->route('clamping.show', $record)->with('success', 'Vehicle clamp recorded successfully.');
    }

    public function show(ClampingRecord $clamping): View
    {
        $this->authorize('view', $clamping);

        $clamping->load(['vehicle.owner', 'officer', 'citation', 'release.releasedByUser']);

        return view('clamping.show', compact('clamping'));
    }
}
