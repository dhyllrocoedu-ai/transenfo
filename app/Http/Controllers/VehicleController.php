<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use App\Http\Requests\UpdateVehicleRequest;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehicleController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Vehicle::class);

        $query = Vehicle::with(['owner', 'driver']);

        if (! auth()->user()->isStaff()) {
            $query->where('owner_id', auth()->id());
        }

        $vehicles = $query
            ->when($request->search, fn ($q, $search) => $q->where('plate_number', 'like', "%{$search}%"))
            ->orderBy('plate_number')
            ->paginate(15)
            ->withQueryString();

        return view('vehicles.index', compact('vehicles'));
    }

    public function create(): View
    {
        $this->authorize('create', Vehicle::class);

        return view('vehicles.create', [
            'drivers' => Driver::orderBy('last_name')->get(),
            'owners' => User::where('role', 'vehicle_owner')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreVehicleRequest $request): RedirectResponse
    {
        $this->authorize('create', Vehicle::class);

        $vehicle = Vehicle::create($request->validated());

        return redirect()->route('vehicles.show', $vehicle)->with('success', 'Vehicle registered successfully.');
    }

    public function show(Vehicle $vehicle): View
    {
        $this->authorize('view', $vehicle);

        $vehicle->load(['owner', 'driver', 'citations.violationType', 'clampingRecords']);

        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle): View
    {
        $this->authorize('update', $vehicle);

        return view('vehicles.edit', [
            'vehicle' => $vehicle,
            'drivers' => Driver::orderBy('last_name')->get(),
            'owners' => User::where('role', 'vehicle_owner')->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateVehicleRequest $request, Vehicle $vehicle): RedirectResponse
    {
        $this->authorize('update', $vehicle);

        $vehicle->update($request->validated());

        return redirect()->route('vehicles.show', $vehicle)->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        $this->authorize('delete', $vehicle);

        $vehicle->delete();

        return redirect()->route('vehicles.index')->with('success', 'Vehicle deleted successfully.');
    }
}
