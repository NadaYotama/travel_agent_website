<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tour_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('destination_id')->constrained()->restrictOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 14, 2);
            $table->unsignedSmallInteger('duration_days');
            $table->unsignedInteger('quota')->default(0);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['is_published', 'start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tour_packages');
    }
};