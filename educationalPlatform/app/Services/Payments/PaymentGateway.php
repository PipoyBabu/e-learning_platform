<?php

namespace App\Services\Payments;

use App\Models\MembershipTransaction;

interface PaymentGateway
{
    /** @return array{gateway_reference: string, transaction_id: int, amount: float} */
    public function createCheckout(MembershipTransaction $transaction, float $amount): array;

    /** @return array{gateway_reference: string, transaction_id: int, amount: float}|null */
    public function resolve(string $gatewayReference): ?array;
}
