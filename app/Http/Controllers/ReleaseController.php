<?php

namespace App\Http\Controllers;

use App\Enums\CitationStatus;
use App\Enums\ClampingStatus;
use App\Http\Requests\StoreReleaseRequest;
use App\Models\ClampingRecord;
use App\Models\VehicleRelease;
use App\Services\CitationNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReleaseController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', VehicleRelease::class);

        $activeClamps = ClampingRecord::with(['vehicle', 'citation'])
            ->where('status', ClampingStatus::Active)
            ->latest('clamped_at')
            ->get();

        $releases = VehicleRelease::with(['clampingRecord.vehicle', 'releasedByUser'])
            ->latest('released_at')
            ->paginate(15);

        return view('releases.index', compact('activeClamps', 'releases'));
    }

    public function create(ClampingRecord $clamping): View
    {
        $this->authorize('create', VehicleRelease::class);
        $this->authorize('view', $clamping);

        $clamping->load(['vehicle.citations', 'citation']);

        $unpaidCitations = $clamping->vehicle->citations()
            ->whereIn('status', [CitationStatus::Issued, CitationStatus::Overdue, CitationStatus::Clamped])
            ->get();

        return view('releases.create', compact('clamping', 'unpaidCitations'));
    }

    public function store(StoreReleaseRequest $request, ClampingRecord $clamping, CitationNumberService $numberService): RedirectResponse
    {
        $this->authorize('create', VehicleRelease::class);

        if (! $clamping->isActive()) {
            return back()->withErrors(['clamping' => 'This clamp has already been released.']);
        }

        $unpaidCount = $clamping->vehicle->citations()
            ->whereIn('status', [CitationStatus::Issued, CitationStatus::Overdue, CitationStatus::Clamped])
            ->count();

        if ($unpaidCount > 0) {
            return back()->withErrors([
                'clamping' => 'All outstanding citations must be paid before release.',
            ]);
        }

        $release = DB::transaction(function () use ($request, $clamping, $numberService) {
            $release = VehicleRelease::create([
                'release_number' => $numberService->releaseNumber(),
                'clamping_record_id' => $clamping->id,
                'released_by' => auth()->id(),
                'notes' => $request->notes,
                'released_at' => now(),
            ]);

            $clamping->update(['status' => ClampingStatus::Released]);

            $clamping->vehicle->citations()
                ->where('status', CitationStatus::Paid)
                ->update(['status' => CitationStatus::Released]);

            return $release;
        });

        return redirect()->route('releases.index')->with('success', "Vehicle released. Release #{$release->release_number}");
    }
}
