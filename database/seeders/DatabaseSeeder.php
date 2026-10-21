<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Jalankan: php artisan migrate:fresh --seed
     *
     * Di production hanya akun admin yang dibuat (lihat UserSeeder);
     * data demo (destinasi, paket, promo, akun contoh) hanya untuk lingkungan lokal/staging.
     */
    public function run(): void
    {
        $seeders = [UserSeeder::class];

        if (! app()->isProduction()) {
            // Urutan penting: foto contoh dulu, lalu destinasi, terakhir paket
            // (paket membutuhkan destinasi dan layanan vendor).
            $seeders[] = DemoImageSeeder::class;
            $seeders[] = DestinationSeeder::class;
            $seeders[] = TourPackageSeeder::class;
        }

        $this->call($seeders);
    }
}
