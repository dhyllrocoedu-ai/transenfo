<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ZoneController extends Controller
{
    public function index(): View
    {
        $this->authorizeAdmin();

        $zones = Zone::with('team')->latest()->get();

        return view('zones.index', compact('zones'));
    }

    public function create(): View
    {
        $this->authorizeAdmin();

        return view('zones.create', ['teams' => Team::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'center_latitude' => ['required', 'numeric'],
            'center_longitude' => ['required', 'numeric'],
            'radius_km' => ['required', 'numeric'],
            'is_active' => ['boolean'],
        ]);

        Zone::create($data);

        return redirect()->route('zones.index')->with('success', 'Zone created successfully.');
    }

    public function edit(Zone $zone): View
    {
        $this->authorizeAdmin();

        return view('zones.edit', ['zone' => $zone, 'teams' => Team::orderBy('name')->get()]);
    }

    public function update(Request $request, Zone $zone): RedirectResponse
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'team_id' => ['nullable', 'exists:teams,id'],
            'center_latitude' => ['required', 'numeric'],
            'center_longitude' => ['required', 'numeric'],
            'radius_km' => ['required', 'numeric'],
            'is_active' => ['boolean'],
        ]);

        $zone->update($data);

        return redirect()->route('zones.index')->with('success', 'Zone updated successfully.');
    }

    protected function authorizeAdmin(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);
    }
}
