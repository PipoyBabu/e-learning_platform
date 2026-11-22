<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use Database\Factories\MembershipTransactionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipTransaction extends Model
{
    /** @use HasFactory<MembershipTransactionFactory> */
    use HasFactory;

    protected $fillable = [
        'member_id', 'membership_level_id', 'reference_no', 'type', 'status',
        'amount_due', 'starts_on', 'expires_on', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => TransactionType::class,
            'status' => TransactionStatus::class,
            'amount_due' => 'decimal:2',
            'starts_on' => 'date',
            'expires_on' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $transaction): void {
            if ($transaction->reference_no) {
                return;
            }

            $date = now()->format('Ymd');
            $sequence = static::where('reference_no', 'like', "TXN-{$date}-%")->count() + 1;
            $transaction->reference_no = sprintf('TXN-%s-%04d', $date, $sequence);
        });
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function level()
    {
        return $this->belongsTo(MembershipLevel::class, 'membership_level_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', TransactionStatus::Pending);
    }

    public function isExpired(): bool
    {
        return $this->expires_on?->isPast() ?? false;
    }
}
