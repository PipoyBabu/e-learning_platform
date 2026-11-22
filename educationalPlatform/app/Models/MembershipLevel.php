<?php

namespace App\Models;

use Database\Factories\MembershipLevelFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipLevel extends Model
{
    /** @use HasFactory<MembershipLevelFactory> */
    use HasFactory;

    protected $fillable = ['name', 'fee', 'validity_months', 'benefits'];

    protected function casts(): array
    {
        return ['fee' => 'decimal:2', 'validity_months' => 'integer'];
    }

    public function members()
    {
        return $this->hasMany(Member::class, 'membership_level_id');
    }

    public function transactions()
    {
        return $this->hasMany(MembershipTransaction::class);
    }
}
