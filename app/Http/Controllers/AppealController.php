<?php

namespace App\Http\Controllers;

use App\Enums\AppealStatus;
use App\Http\Requests\StoreAppealRequest;
use App\Http\Requests\UpdateAppealRequest;
use App\Models\Appeal;
use App\Models\Citation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppealController extends Controller
{
    public function index(Request $request): View
    {
        $query = Appeal::with(['citation.vehicle', 'submitter', 'reviewer']);

        if (! auth()->user()->isStaff()) {
            $query->where('submitted_by', auth()->id());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appeals = $query->latest('submitted_at')->paginate(15)->withQueryString();

        return view('appeals.index', compact('appeals'));
    }

    public function create(): View
    {
        $citations = Citation::query()
            ->whereHas('vehicle', fn ($query) => $query->where('owner_id', auth()->id()))
            ->with(['violationType', 'vehicle'])
            ->latest('issued_at')
            ->get();

        return view('appeals.create', compact('citations'));
    }

    public function store(StoreAppealRequest $request): RedirectResponse
    {
        $citation = Citation::findOrFail($request->citation_id);

        if ($citation->vehicle?->owner_id !== auth()->id()) {
            abort(403);
        }

        Appeal::create([
            'citation_id' => $citation->id,
            'submitted_by' => auth()->id(),
            'reason' => $request->reason,
            'description' => $request->description,
            'status' => AppealStatus::Submitted,
            'submitted_at' => now(),
        ]);

        return redirect()->route('appeals.index')->with('success', 'Appeal submitted successfully.');
    }

    public function show(Appeal $appeal): View
    {
        $this->authorize('view', $appeal);

        $appeal->load(['citation.violationType', 'citation.vehicle', 'submitter', 'reviewer']);

        return view('appeals.show', compact('appeal'));
    }

    public function edit(Appeal $appeal): View
    {
        $this->authorize('update', $appeal);

        return view('appeals.edit', compact('appeal'));
    }

    public function update(UpdateAppealRequest $request, Appeal $appeal): RedirectResponse
    {
        $this->authorize('update', $appeal);

        $appeal->update([
            'status' => $request->status,
            'reviewed_by' => auth()->id(),
            'decision_notes' => $request->decision_notes,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('appeals.show', $appeal)->with('success', 'Appeal updated successfully.');
    }
}
