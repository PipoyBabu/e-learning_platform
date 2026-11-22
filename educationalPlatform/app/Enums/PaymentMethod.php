<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case GCash = 'GCash';
    case Card = 'Card';
    case BankTransfer = 'Bank transfer';
    case Cash = 'Cash at counter';
}
