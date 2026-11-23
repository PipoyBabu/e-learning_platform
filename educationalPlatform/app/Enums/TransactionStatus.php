<?php

namespace App\Enums;

enum TransactionStatus: string
{
    case Pending = 'Pending';
    case Paid = 'Paid';
    case Approved = 'Approved';
    case Expired = 'Expired';
    case Cancelled = 'Cancelled';

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-100 text-amber-800',
            self::Paid => 'bg-blue-100 text-blue-800',
            self::Approved => 'bg-emerald-100 text-emerald-800',
            self::Cancelled => 'bg-red-100 text-red-800',
            self::Expired => 'bg-slate-200 text-slate-700',
        };
    }
}
