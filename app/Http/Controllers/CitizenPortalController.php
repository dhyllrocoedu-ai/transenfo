<?php

namespace App\Http\Controllers;

use App\Models\Citation;
use App\Models\ClampingRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CitizenPortalController extends Controller
{
    public function citationLookup(Request $request): View
    {
        return view('citizen.citation-lookup');
    }

    public function citationSearch(Request $request)
    {
        $request->validate([
            'search' => 'required|string|min:3',
        ]);

        $search = $request->input('search');

        $citation = Citation::with(['violationType', 'vehicle', 'driver', 'evidence'])
            ->where(function ($query) use ($search) {
                $query->where('citation_number', 'like', "%{$search}%")
                    ->orWhereHas('vehicle', fn ($v) => $v->where('plate_number', 'like', "%{$search}%"));
            })
            ->first();

        if (! $citation) {
            return back()->with('error', 'No citation found matching your search.');
        }

        return view('citizen.citation-detail', compact('citation'));
    }

    public function citationDetail(Citation $citation): View
    {
        $citation->load(['violationType', 'vehicle', 'driver', 'evidence', 'payment']);

        return view('citizen.citation-detail', compact('citation'));
    }

    public function clampingRequest(): View
    {
        return view('citizen.clamping-request');
    }

    public function storeClampingRequest(Request $request)
    {
        $data = $request->validate([
            'requester_name' => 'required|string|max:255',
            'requester_phone' => 'required|string|max:20',
            'requester_email' => 'required|email',
            'location_address' => 'required|string|max:500',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'vehicle_plate' => 'required|string|max:20',
            'vehicle_description' => 'nullable|string',
            'evidence_photo' => 'required|image|max:5120',
            'additional_notes' => 'nullable|string|max:1000',
        ]);

        // Store evidence photo
        $photoPath = $request->file('evidence_photo')->store('clamping-requests', 'public');
        $data['evidence_photo'] = $photoPath;
        $data['status'] = 'pending';

        // Create clamping request record
        $request_data = ClampingRequest::create($data);

        return view('citizen.clamping-success');
    }

    public function clampingSuccess(): View
    {
        return view('citizen.clamping-success');
    }
}
