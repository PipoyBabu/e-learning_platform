<?php

namespace App\Services\Payments;

use App\Models\MembershipTransaction;
use Illuminate\Contracts\Session\Session;

class SimulatedGateway implements PaymentGateway
{
    public function __construct(private readonly Session $session) {}

    public function createCheckout(MembershipTransaction $transaction, float $amount): array
    {
        $checkout = [
            'gateway_reference' => 'SIM-'.strtoupper(substr(bin2hex(random_bytes(4)), 0, 6)),
            'transaction_id' => $transaction->id,
            'amount' => round($amount, 2),
        ];

        $this->session->put($this->key($checkout['gateway_reference']), $checkout);

        return $checkout;
    }

    public function resolve(string $gatewayReference): ?array
    {
        return $this->session->get($this->key($gatewayReference));
    }

    private function key(string $gatewayReference): string
    {
        return "simulated_gateway.checkout.{$gatewayReference}";
    }
}
