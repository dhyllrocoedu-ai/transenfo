<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDriverRequest;
use App\Http\Requests\UpdateDriverRequest;
use App\Models\Driver;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DriverController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Driver::class);

        $drivers = Driver::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('license_number', 'like', "%{$search}%");
                });
            })
            ->orderBy('last_name')
            ->paginate(15)
            ->withQueryString();

        return view('drivers.index', compact('drivers'));
    }

    public function create(): View
    {
        $this->authorize('create', Driver::class);

        return view('drivers.create');
    }

    public function store(StoreDriverRequest $request): RedirectResponse
    {
        $this->authorize('create', Driver::class);

        $driver = Driver::create($request->validated());

        return redirect()->route('drivers.show', $driver)->with('success', 'Driver registered successfully.');
    }

    public function show(Driver $driver): View
    {
        $this->authorize('view', $driver);

        $driver->load(['citations.violationType', 'citations.vehicle', 'vehicles']);

        return view('drivers.show', compact('driver'));
    }

    public function edit(Driver $driver): View
    {
        $this->authorize('update', $driver);

        return view('drivers.edit', compact('driver'));
    }

    public function update(UpdateDriverRequest $request, Driver $driver): RedirectResponse
    {
        $this->authorize('update', $driver);

        $driver->update($request->validated());

        return redirect()->route('drivers.show', $driver)->with('success', 'Driver updated successfully.');
    }

    public function destroy(Driver $driver): RedirectResponse
    {
        $this->authorize('delete', $driver);

        $driver->delete();

        return redirect()->route('drivers.index')->with('success', 'Driver deleted successfully.');
    }
}
