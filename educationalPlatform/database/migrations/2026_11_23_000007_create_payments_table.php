<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_transaction_id')->constrained('membership_transactions')->restrictOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('status');
            $table->string('gateway_reference')->unique();
            $table->string('method');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['membership_transaction_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
