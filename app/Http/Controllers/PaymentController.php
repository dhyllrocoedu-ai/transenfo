<?php

namespace App\Http\Controllers;

use App\Enums\CitationStatus;
use App\Enums\PaymentMethod;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Citation;
use App\Models\Payment;
use App\Services\CitationNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Payment::class);

        $query = Payment::with(['citation.vehicle', 'cashier'])->whereNotNull('paid_at');

        if (! auth()->user()->isStaff()) {
            $query->whereHas('citation.vehicle', fn ($q) => $q->where('owner_id', auth()->id()));
        }

        $payments = $query->latest('paid_at')->paginate(15);

        return view('payments.index', compact('payments'));
    }

    public function create(Request $request): View
    {
        $this->authorize('create', Payment::class);

        $citation = null;
        if ($request->filled('citation_number')) {
            $citation = Citation::with(['violationType', 'vehicle', 'payment'])
                ->where('citation_number', $request->citation_number)
                ->first();
        } elseif ($request->filled('citation_id')) {
            $citation = Citation::with(['violationType', 'vehicle', 'payment'])->find($request->citation_id);
        }

        return view('payments.create', [
            'citation' => $citation,
            'paymentMethods' => PaymentMethod::cases(),
        ]);
    }

    public function store(StorePaymentRequest $request, CitationNumberService $numberService): RedirectResponse
    {
        $this->authorize('create', Payment::class);

        $citation = Citation::with('payment')->findOrFail($request->citation_id);

        if ($citation->payment) {
            return back()->withErrors(['citation_id' => 'This citation has already been paid.']);
        }

        if (! $citation->isPayable()) {
            return back()->withErrors(['citation_id' => 'This citation is not eligible for payment.']);
        }

        $payment = DB::transaction(function () use ($request, $citation, $numberService) {
            $payment = Payment::create([
                'receipt_number' => $numberService->receiptNumber(),
                'citation_id' => $citation->id,
                'cashier_id' => auth()->id(),
                'amount' => $citation->penalty_amount,
                'payment_method' => $request->payment_method,
                'reference_number' => $request->reference_number,
                'notes' => $request->notes,
                'paid_at' => now(),
            ]);

            $citation->update(['status' => CitationStatus::Paid]);

            return $payment;
        });

        return redirect()->route('payments.show', $payment)->with('success', 'Payment recorded successfully.');
    }

    public function show(Payment $payment): View
    {
        $this->authorize('view', $payment);

        $payment->load(['citation.violationType', 'citation.vehicle', 'cashier']);

        return view('payments.show', compact('payment'));
    }
}
