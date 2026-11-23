<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereIn('email', DB::table('members')->select('email'))
            ->update(['role' => 'member']);
    }

    public function down(): void
    {
        // Existing member accounts should not be silently promoted during rollback.
    }
};
