<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Exceptions\TransactionRuleException;
use App\Http\Requests\GatewayCallbackRequest;
use App\Http\Requests\InitiatePaymentRequest;
use App\Models\MembershipTransaction;
use App\Models\Payment;
use App\Services\Payments\PaymentGateway;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private readonly PaymentGateway $gateway,
        private readonly TransactionService $transactionService,
    ) {}

    public function initiate(InitiatePaymentRequest $request, MembershipTransaction $transaction): RedirectResponse
    {
        try {
            $amount = (float) $request->validated('amount');
            $this->transactionService->validatePayment($transaction, $amount);
            $checkout = $this->gateway->createCheckout($transaction, $amount);
        } catch (TransactionRuleException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return to_route('gateway.checkout', $checkout['gateway_reference']);
    }

    public function checkout(string $gatewayReference): View
    {
        $checkout = $this->gateway->resolve($gatewayReference);

        abort_unless($checkout, 404);

        return view('gateway.checkout', compact('checkout'));
    }

    public function callback(GatewayCallbackRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $checkout = $this->gateway->resolve($data['gateway_reference']);

        abort_unless($checkout, 404);

        if (Payment::where('gateway_reference', $data['gateway_reference'])->exists()) {
            return to_route('transactions.show', $checkout['transaction_id'])->with('success', 'This checkout has already been processed.');
        }

        try {
            $transaction = MembershipTransaction::findOrFail($checkout['transaction_id']);
            $status = $data['outcome'] === 'success' ? PaymentStatus::Succeeded : PaymentStatus::Failed;
            $this->transactionService->recordPayment($transaction, $checkout['amount'], $data['method'], $status, $data['gateway_reference']);
        } catch (TransactionRuleException $exception) {
            return to_route('transactions.show', $checkout['transaction_id'])->with('error', $exception->getMessage());
        }

        $message = $status === PaymentStatus::Succeeded ? 'Payment recorded successfully.' : 'Failed payment recorded; transaction status is unchanged.';

        return to_route('transactions.show', $checkout['transaction_id'])->with($status === PaymentStatus::Succeeded ? 'success' : 'error', $message);
    }
}
