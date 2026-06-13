<?php

namespace App\Http\Controllers;

use App\Models\Citation;
use App\Models\ClampingRecord;
use App\Models\Vehicle;
use Illuminate\View\View;

class OwnerPortalController extends Controller
{
    public function citations(): View
    {
        $vehicleIds = $this->ownerVehicleIds();

        $citations = Citation::with(['violationType', 'vehicle', 'payment'])
            ->whereIn('vehicle_id', $vehicleIds)
            ->latest('issued_at')
            ->paginate(15);

        return view('owner.citations', compact('citations'));
    }

    public function vehicles(): View
    {
        $vehicles = Vehicle::with(['driver', 'citations'])
            ->where('owner_id', auth()->id())
            ->orderBy('plate_number')
            ->paginate(15);

        return view('owner.vehicles', compact('vehicles'));
    }

    public function clamping(): View
    {
        $vehicleIds = $this->ownerVehicleIds();

        $records = ClampingRecord::with(['vehicle', 'citation', 'release'])
            ->whereIn('vehicle_id', $vehicleIds)
            ->latest('clamped_at')
            ->paginate(15);

        return view('owner.clamping', compact('records'));
    }

    /**
     * @return \Illuminate\Support\Collection<int, int>
     */
    protected function ownerVehicleIds()
    {
        return Vehicle::where('owner_id', auth()->id())->pluck('id');
    }
}
