<?php

namespace Database\Seeders;

use App\Support\DemoImagePublisher;
use Illuminate\Database\Seeder;

/**
 * Menyiapkan foto contoh di disk "public" sebelum destinasi dan paket diisi,
 * supaya kolom thumbnail dan package_images.path menunjuk berkas yang nyata.
 */
class DemoImageSeeder extends Seeder
{
    public function run(): void
    {
        // $result = app(DemoImagePublisher::class)->publish();

        // if ($result['written'] > 0) {
        //     $this->command?->info("{$result['written']} foto contoh dipublikasikan ke disk \"public\".");
        // }
    }
}
