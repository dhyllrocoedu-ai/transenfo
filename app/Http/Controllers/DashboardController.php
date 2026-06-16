<?php

namespace App\Http\Controllers;

use App\Enums\AppealStatus;
use App\Enums\CitationStatus;
use App\Enums\ClampingStatus;
use App\Enums\Role;
use App\Models\Appeal;
use App\Models\Citation;
use App\Models\ClampingRecord;
use App\Models\Payment;
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
            'pending_appeals' => Appeal::whereIn('status', [AppealStatus::Submitted, AppealStatus::UnderReview])->count(),
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

        $recentCitations = Citation::with(['violationType'])
            ->latest('issued_at')
            ->take(5)
            ->get();

        $recentPayments = Payment::with('citation')
            ->latest('paid_at')
            ->take(5)
            ->get();

        $pendingAppeals = Appeal::with('citation')
            ->latest('submitted_at')
            ->take(5)
            ->get();

        $activeClampRecords = ClampingRecord::with('citation')
            ->where('status', ClampingStatus::Active)
            ->latest('clamped_at')
            ->take(5)
            ->get();

        $recentActivity = collect([
            ...$recentCitations->map(fn (Citation $citation) => [
                'type' => 'citation',
                'icon' => 'bi-receipt',
                'title' => 'Citation issued',
                'description' => $citation->driver_name.' • '.$citation->citation_number,
                'timestamp' => $citation->issued_at,
                'timestamp_label' => $citation->issued_at?->diffForHumans(),
            ]),
            ...$recentPayments->map(fn (Payment $payment) => [
                'type' => 'payment',
                'icon' => 'bi-cash-stack',
                'title' => 'Payment received',
                'description' => 'Receipt '.$payment->receipt_number.' • ₱'.number_format($payment->amount, 2),
                'timestamp' => $payment->paid_at,
                'timestamp_label' => $payment->paid_at?->diffForHumans(),
            ]),
            ...$pendingAppeals->map(fn (Appeal $appeal) => [
                'type' => 'appeal',
                'icon' => 'bi-chat-square-text',
                'title' => 'Appeal submitted',
                'description' => $appeal->citation?->citation_number ?? 'Appeal pending review',
                'timestamp' => $appeal->submitted_at,
                'timestamp_label' => $appeal->submitted_at?->diffForHumans(),
            ]),
            ...$activeClampRecords->map(fn (ClampingRecord $clamp) => [
                'type' => 'clamp',
                'icon' => 'bi-lock',
                'title' => 'Vehicle clamped',
                'description' => $clamp->vehicle_plate,
                'timestamp' => $clamp->clamped_at,
                'timestamp_label' => $clamp->clamped_at?->diffForHumans(),
            ]),
        ])->sortByDesc('timestamp')->take(8)->values();

        return view('dashboard.index', compact(
            'stats',
            'citationsByMonth',
            'paymentsByMonth',
            'recentCitations',
            'recentPayments',
            'pendingAppeals',
            'activeClampRecords',
            'recentActivity',
        ));
    }

    protected function ownerDashboard($user): View
    {
        $stats = [
            'my_citations' => Citation::where('issued_by', $user->id)->count(),
            'unpaid' => Citation::where('issued_by', $user->id)
                ->whereIn('status', [CitationStatus::Issued, CitationStatus::Overdue, CitationStatus::Clamped])
                ->count(),
            'active_clamps' => ClampingRecord::where('clamped_by', $user->id)
                ->where('status', ClampingStatus::Active)
                ->count(),
        ];

        $recentCitations = Citation::with(['violationType'])
            ->where('issued_by', $user->id)
            ->latest('issued_at')
            ->limit(5)
            ->get();

        return view('owner.dashboard', compact('stats', 'recentCitations'));
    }
}
