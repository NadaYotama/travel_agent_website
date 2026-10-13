<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tour_package_vendor_service', function (Blueprint $table) {
            $table->id();
            
            // --- TAMBAHKAN DUA BARIS KODE DI BAWAH INI ---
            // Menghubungkan ke tabel tour_packages (id)
            $table->foreignId('tour_package_id')->constrained()->onDelete('cascade');
            
            // Menghubungkan ke tabel vendor_services (id)
            $table->foreignId('vendor_service_id')->constrained()->onDelete('cascade');
            // ----------------------------------------------

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_package_vendor_service');
    }
};
