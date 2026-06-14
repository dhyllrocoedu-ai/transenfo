<?php

namespace App\Http\Controllers;

use App\Models\Citation;
use App\Models\ClampingRecord;
use App\Models\SavedSearch;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $results = collect();
        $query = $request->input('q');
        $type = $request->input('type', 'all');

        if ($query) {
            $queryLike = "%{$query}%";

            if (in_array($type, ['all', 'citation'], true)) {
                $results = $results->merge(
                    Citation::with(['vehicle', 'violationType', 'driver'])
                        ->where('citation_number', 'like', $queryLike)
                        ->orWhere('location', 'like', $queryLike)
                        ->orWhereHas('vehicle', fn ($q) => $q->where('plate_number', 'like', $queryLike))
                        ->orWhereHas('driver', fn ($q) => $q->where('first_name', 'like', $queryLike)->orWhere('last_name', 'like', $queryLike))
                        ->get()
                        ->map(fn ($c) => ['type' => 'Citation', 'id' => $c->id, 'label' => $c->citation_number, 'subtitle' => $c->vehicle?->plate_number, 'url' => route('citations.show', $c)])
                );
            }

            if (in_array($type, ['all', 'vehicle'], true)) {
                $results = $results->merge(
                    Vehicle::with('owner')
                        ->where('plate_number', 'like', $queryLike)
                        ->orWhere('make', 'like', $queryLike)
                        ->orWhere('model', 'like', $queryLike)
                        ->get()
                        ->map(fn ($v) => ['type' => 'Vehicle', 'id' => $v->id, 'label' => $v->plate_number, 'subtitle' => "{$v->make} {$v->model}", 'url' => route('vehicles.show', $v)])
                );
            }
        }

        $savedSearches = SavedSearch::where('user_id', auth()->id())->latest()->get();

        return view('search.index', compact('results', 'savedSearches', 'query', 'type'));
    }

    public function save(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'type' => ['required', 'string'],
            'criteria' => ['required', 'array'],
        ]);

        SavedSearch::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'criteria' => $validated['criteria'],
        ]);

        return back()->with('success', 'Search saved.');
    }

    public function destroy(SavedSearch $search)
    {
        abort_unless($search->user_id === auth()->id(), 403);
        $search->delete();

        return back()->with('success', 'Saved search removed.');
    }
}
