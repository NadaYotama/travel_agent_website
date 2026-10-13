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
            $table->foreignId('booking_id')->constrained()->cascadeOnDelete();
            $table->string('method', 30);
            $table->decimal('amount', 14, 2);
            $table->string('status', 20)->default('pending');
            $table->string('proof')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('gateway_ref')->nullable()->index();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};