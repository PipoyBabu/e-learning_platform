<?php

namespace App\Models;

use Database\Factories\MemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    /** @use HasFactory<MemberFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'membership_number', 'first_name', 'last_name', 'email', 'phone',
        'membership_category_id', 'membership_level_id', 'is_active', 'joined_on',
        'address', 'latitude', 'longitude',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'joined_on' => 'date',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $member): void {
            if ($member->membership_number) {
                return;
            }

            $year = now()->year;
            $sequence = 1;

            do {
                $membershipNumber = sprintf('PUI-%d-%06d', $year, $sequence);
                $sequence++;
            } while (static::withTrashed()->where('membership_number', $membershipNumber)->exists());

            $member->membership_number = $membershipNumber;
        });
    }

    public function category()
    {
        return $this->belongsTo(MembershipCategory::class, 'membership_category_id');
    }

    public function level()
    {
        return $this->belongsTo(MembershipLevel::class, 'membership_level_id');
    }

    public function transactions()
    {
        return $this->hasMany(MembershipTransaction::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
