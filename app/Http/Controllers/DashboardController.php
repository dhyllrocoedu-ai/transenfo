<?php

namespace App\Http\Controllers;

use App\Enums\CitationStatus;
use App\Enums\ClampingStatus;
use App\Enums\Role;
use App\Models\Citation;
use App\Models\ClampingRecord;
use App\Models\Payment;
use App\Models\Vehicle;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        if ($user->isRole(Role::VehicleOwner)) {
            return $this->ownerDashboard($user);
        }

        $stats = [
            'total_citations' => Citation::count(),
            'unpaid_citations' => Citation::whereIn('status', [
                CitationStatus::Issued,
                CitationStatus::Overdue,
                CitationStatus::Clamped,
            ])->count(),
            'payments_today' => Payment::whereDate('paid_at', today())->sum('amount'),
            'active_clamps' => ClampingRecord::where('status', ClampingStatus::Active)->count(),
        ];

        $citationsByMonth = Citation::query()
            ->where('issued_at', '>=', now()->subMonths(6))
            ->get()
            ->groupBy(fn (Citation $c) => $c->issued_at->format('Y-m'))
            ->map->count()
            ->sortKeys();

        $paymentsByMonth = Payment::query()
            ->where('paid_at', '>=', now()->subMonths(6))
            ->get()
            ->groupBy(fn (Payment $p) => $p->paid_at->format('Y-m'))
            ->map(fn ($group) => $group->sum('amount'))
            ->sortKeys();

        return view('dashboard.index', compact('stats', 'citationsByMonth', 'paymentsByMonth'));
    }

    protected function ownerDashboard($user): View
    {
        $vehicleIds = Vehicle::where('owner_id', $user->id)->pluck('id');

        $stats = [
            'my_vehicles' => $vehicleIds->count(),
            'my_citations' => Citation::whereIn('vehicle_id', $vehicleIds)->count(),
            'unpaid' => Citation::whereIn('vehicle_id', $vehicleIds)
                ->whereIn('status', [CitationStatus::Issued, CitationStatus::Overdue, CitationStatus::Clamped])
                ->count(),
            'active_clamps' => ClampingRecord::whereIn('vehicle_id', $vehicleIds)
                ->where('status', ClampingStatus::Active)
                ->count(),
        ];

        $recentCitations = Citation::with(['violationType', 'vehicle'])
            ->whereIn('vehicle_id', $vehicleIds)
            ->latest('issued_at')
            ->limit(5)
            ->get();

        return view('owner.dashboard', compact('stats', 'recentCitations'));
    }
}
