<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_package_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('day');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['tour_package_id', 'day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('itineraries');
    }
};