<?php

namespace App\Http\Controllers;

use App\Models\Citation;
use App\Models\ClampingRequest;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontDeskController extends Controller
{
    public function index(): View
    {
        return view('frontdesk.index');
    }

    public function search(Request $request): View
    {
        $plateNumber = $request->input('plate_number');
        $citationNumber = $request->input('citation_number');

        $citation = null;
        $vehicle = null;

        if ($citationNumber) {
            $citation = Citation::with(['vehicle', 'violationType', 'payment', 'driver'])
                ->where('citation_number', $citationNumber)
                ->first();
        } elseif ($plateNumber) {
            $vehicle = Vehicle::with('owner')
                ->where('plate_number', $plateNumber)
                ->first();

            if ($vehicle) {
                $citation = Citation::with(['violationType', 'payment'])
                    ->where('vehicle_id', $vehicle->id)
                    ->latest('issued_at')
                    ->first();
            }
        }

        return view('frontdesk.index', compact('citation', 'vehicle', 'plateNumber', 'citationNumber'));
    }
}
