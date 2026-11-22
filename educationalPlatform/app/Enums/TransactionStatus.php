<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case Pending = 'Pending';
    case Paid = 'Paid';
    case Approved = 'Approved';
    case Expired = 'Expired';
    case Cancelled = 'Cancelled';
}
