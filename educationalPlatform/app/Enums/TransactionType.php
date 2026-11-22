<?php

namespace App\Enums;

enum TransactionType: string
{
    case Application = 'Application';
    case Renewal = 'Renewal';
}
