<?php

namespace Database\Seeders;

use App\Models\Destination;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->destinations() as $d) {
            // Dicocokkan lewat slug, sehingga seeder aman dijalankan ulang.
            Destination::firstOrCreate(
                ['slug' => Str::slug($d['name'])],
                [
                    'name'        => $d['name'],
                    'country'     => $d['country'],
                    'city'        => $d['city'],
                    'description' => $d['description'],
                ]
            );
        }
    }

    private function destinations(): array
    {
        return [
            [
                'name'        => 'Bali',
                'country'     => 'Indonesia',
                'city'        => 'Denpasar',
                'description' => 'Pulau dengan pantai, sawah berteras, dan pura yang masih hidup dalam keseharian warganya. Cocok untuk liburan keluarga maupun pasangan.',
            ],
            [
                'name'        => 'Labuan Bajo',
                'country'     => 'Indonesia',
                'city'        => 'Labuan Bajo',
                'description' => 'Pintu masuk ke Taman Nasional Komodo, dengan pulau-pulau kecil, spot snorkeling, dan pemandangan matahari terbenam dari atas kapal.',
            ],
            [
                'name'        => 'Yogyakarta',
                'country'     => 'Indonesia',
                'city'        => 'Yogyakarta',
                'description' => 'Kota budaya dengan Candi Borobudur dan Prambanan, keraton, serta kuliner khas yang mudah dijelajahi dalam waktu singkat.',
            ],
            [
                'name'        => 'Raja Ampat',
                'country'     => 'Indonesia',
                'city'        => 'Waisai',
                'description' => 'Gugusan pulau karang di Papua Barat dengan keanekaragaman laut yang termasuk paling kaya di dunia.',
            ],
            [
                'name'        => 'Lombok',
                'country'     => 'Indonesia',
                'city'        => 'Mataram',
                'description' => 'Pantai yang lebih tenang dari Bali, desa Sasak, dan jalur pendakian Gunung Rinjani.',
            ],
            [
                'name'        => 'Kyoto',
                'country'     => 'Jepang',
                'city'        => 'Kyoto',
                'description' => 'Bekas ibu kota Jepang dengan ribuan kuil, taman, dan distrik geisha. Terbaik dikunjungi saat musim sakura atau musim gugur.',
            ],
            [
                'name'        => 'Cappadocia',
                'country'     => 'Turki',
                'city'        => 'Göreme',
                'description' => 'Lanskap batuan unik di Anatolia tengah, terkenal dengan balon udara saat matahari terbit dan rumah gua yang dijadikan hotel.',
            ],
        ];
    }
}