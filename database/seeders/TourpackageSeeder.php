<?php

namespace Database\Seeders;

use App\Models\Destination;
use App\Models\Promo;
use App\Models\TourPackage;
use App\Models\Vendor;
use App\Models\VendorService;
use App\Support\DemoGallery;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Data demo: layanan vendor, paket wisata beserta itinerary, dan dua kode promo.
 * Bergantung pada UserSeeder (profil vendor) dan DestinationSeeder.
 */
class TourPackageSeeder extends Seeder
{
    public function run(): void
    {
        $services = $this->seedVendorServices();

        foreach ($this->packages() as $data) {
            $destination = Destination::where('slug', Str::slug($data['destination']))->first();

            if (! $destination) {
                continue;
            }

            $start = now()->addDays($data['starts_in'])->startOfDay();

            $package = TourPackage::firstOrCreate(
                ['slug' => Str::slug($data['title'])],
                [
                    'destination_id' => $destination->id,
                    'title'          => $data['title'],
                    'description'    => $data['description'],
                    'price'          => $data['price'],
                    'duration_days'  => $data['days'],
                    'quota'          => $data['quota'],
                    'start_date'     => $start->toDateString(),
                    'end_date'       => $start->copy()->addDays($data['days'] - 1)->toDateString(),
                    'is_published'   => $data['published'] ?? true,
                ]
            );

            // Itinerary hanya dibuat saat paket baru, agar tidak dobel bila seeder diulang.
            if ($package->wasRecentlyCreated) {
                $package->itineraries()->createMany(
                    collect($data['itinerary'])->values()->map(fn (array $row, int $i) => [
                        'day'         => $i + 1,
                        'title'       => $row[0],
                        'description' => $row[1],
                    ])->all()
                );
            }

            $this->attachPhoto($package);

            $serviceIds = collect($data['services'] ?? [])
                ->filter(fn (string $key) => isset($services[$key]))
                ->map(fn (string $key) => $services[$key]->id)
                ->all();

            if ($serviceIds) {
                $package->vendorServices()->syncWithoutDetaching($serviceIds);
            }
        }

        $this->seedPromos();
    }

    /**
     * Pasang foto contoh bila paket belum punya gambar sama sekali,
     * supaya unggahan asli dari admin tidak pernah tertimpa.
     */
    private function attachPhoto(TourPackage $package): void
    {
        if ($package->images()->exists()) {
            return;
        }

        $path = DemoGallery::packagePath($package->slug);

        if ($path !== null) {
            $package->images()->create(['path' => $path]);
        }
    }

    /** @return array<string, VendorService> */
    private function seedVendorServices(): array
    {
        $vendor = Vendor::first();

        if (! $vendor) {
            return [];
        }

        $definitions = [
            'hotel-sanur' => ['hotel',     'Hotel Pantai Sanur (per kamar per malam)',         650000],
            'hotel-bajo'  => ['hotel',     'Resort Labuan Bajo (per kamar per malam)',         800000],
            'bus'         => ['transport', 'Bus Pariwisata 30 Seat (per hari)',               2500000],
            'phinisi'     => ['transport', 'Kapal Phinisi Labuan Bajo (per hari)',            6500000],
            'guide'       => ['guide',     'Pemandu Lokal Berbahasa Indonesia (per hari)',      450000],
        ];

        $services = [];
        foreach ($definitions as $key => [$type, $name, $price]) {
            $services[$key] = VendorService::firstOrCreate(
                ['vendor_id' => $vendor->id, 'name' => $name],
                ['type' => $type, 'price' => $price]
            );
        }

        return $services;
    }

    private function seedPromos(): void
    {
        Promo::firstOrCreate(
            ['code' => 'HEMAT10'],
            [
                'discount_type'  => 'percent',
                'discount_value' => 10,
                'valid_until'    => now()->addMonths(3)->toDateString(),
                'quota'          => 50,
            ]
        );

        Promo::firstOrCreate(
            ['code' => 'DISKON500K'],
            [
                'discount_type'  => 'fixed',
                'discount_value' => 500000,
                'valid_until'    => now()->addMonths(2)->toDateString(),
                'quota'          => 20,
            ]
        );
    }

    private function packages(): array
    {
        return [
            [
                'destination' => 'Bali',
                'title'       => 'Bali Timur 4 Hari 3 Malam',
                'description' => 'Menjelajahi sisi timur Bali yang lebih tenang: Tirta Gangga, Pura Lempuyang, Pantai Virgin, dan desa adat Tenganan. Termasuk hotel, transportasi, dan pemandu lokal.',
                'price'       => 3450000,
                'days'        => 4,
                'quota'       => 12,
                'starts_in'   => 30,
                'services'    => ['hotel-sanur', 'bus', 'guide'],
                'itinerary'   => [
                    ['Tiba di Denpasar', 'Penjemputan di bandara, check-in hotel di Sanur, dan makan malam seafood di Jimbaran.'],
                    ['Karangasem dan Tirta Gangga', 'Pura Lempuyang, Istana Air Tirta Gangga, lalu snorkeling di Pantai Virgin.'],
                    ['Desa Tenganan dan Ubud', 'Mengunjungi desa Bali Aga Tenganan, pasar seni Ubud, dan pertunjukan tari malam hari.'],
                    ['Belanja dan kepulangan', 'Waktu bebas untuk oleh-oleh, lalu diantar ke bandara.'],
                ],
            ],
            [
                'destination' => 'Labuan Bajo',
                'title'       => 'Open Trip Labuan Bajo 3D2N',
                'description' => 'Pelayaran dua malam di atas kapal phinisi ke Pulau Padar, Pink Beach, dan Pulau Komodo. Peserta berbagi kabin, cocok untuk pejalan solo maupun kelompok kecil.',
                'price'       => 2890000,
                'days'        => 3,
                'quota'       => 6,
                'starts_in'   => 21,
                'services'    => ['phinisi', 'hotel-bajo', 'guide'],
                'itinerary'   => [
                    ['Naik kapal dan Pulau Kelor', 'Berkumpul di pelabuhan, snorkeling di Pulau Kelor, dan menikmati matahari terbenam di Pulau Kalong.'],
                    ['Padar, Komodo, dan Pink Beach', 'Mendaki Pulau Padar saat pagi, trekking melihat komodo, lalu snorkeling di Pink Beach.'],
                    ['Manta Point dan kembali', 'Snorkeling di Manta Point, sarapan di kapal, dan tiba kembali di pelabuhan.'],
                ],
            ],
            [
                'destination' => 'Yogyakarta',
                'title'       => 'Yogyakarta Heritage 3 Hari',
                'description' => 'Candi Borobudur saat matahari terbit, Keraton, Taman Sari, dan makan gudeg di tempat yang dipilih warga lokal.',
                'price'       => 1850000,
                'days'        => 3,
                'quota'       => 15,
                'starts_in'   => 14,
                'services'    => ['bus', 'guide'],
                'itinerary'   => [
                    ['Keraton dan Taman Sari', 'Tur keraton bersama pemandu, berjalan di Taman Sari, dan makan malam di sekitar Malioboro.'],
                    ['Borobudur dan Prambanan', 'Naik ke Borobudur pada pagi hari, lalu Candi Prambanan menjelang sore.'],
                    ['Kuliner dan oleh-oleh', 'Sarapan gudeg, belanja batik di Beringharjo, kemudian diantar ke stasiun atau bandara.'],
                ],
            ],
            [
                'destination' => 'Raja Ampat',
                'title'       => 'Raja Ampat Island Hopping 5D4N',
                'description' => 'Lima hari berpindah antar pulau di Raja Ampat: Wayag, Piaynemo, dan spot snorkeling dengan terumbu karang yang masih utuh. Menginap di homestay lokal.',
                'price'       => 8900000,
                'days'        => 5,
                'quota'       => 8,
                'starts_in'   => 60,
                'itinerary'   => [
                    ['Tiba di Sorong', 'Penjemputan di bandara dan menyeberang ke Waisai dengan kapal cepat.'],
                    ['Piaynemo', 'Mendaki titik pandang Piaynemo dan snorkeling di sekitar Pulau Arborek.'],
                    ['Wayag', 'Perjalanan satu hari penuh ke gugusan karst Wayag.'],
                    ['Pasir Timbul dan Friwen', 'Bersantai di pasir timbul, melihat burung cenderawasih di Friwen, dan snorkeling sore hari.'],
                    ['Kembali ke Sorong', 'Menyeberang kembali dan diantar ke bandara.'],
                ],
            ],
            [
                'destination' => 'Kyoto',
                'title'       => 'Kyoto & Osaka 6 Hari',
                'description' => 'Enam hari menyusuri Kyoto dan Osaka: Fushimi Inari, Arashiyama, Kinkaku-ji, dan wisata kuliner di Dotonbori. Termasuk tiket kereta antar kota dan hotel.',
                'price'       => 17900000,
                'days'        => 6,
                'quota'       => 20,
                'starts_in'   => 90,
                'itinerary'   => [
                    ['Tiba di Osaka', 'Check-in hotel dan makan malam di Dotonbori.'],
                    ['Kastil Osaka dan Umeda', 'Kastil Osaka pada pagi hari, lalu waktu bebas di Umeda Sky Building.'],
                    ['Pindah ke Kyoto', 'Naik kereta ke Kyoto dan mengunjungi Fushimi Inari.'],
                    ['Arashiyama', 'Hutan bambu, Kuil Tenryu-ji, dan naik kereta Sagano.'],
                    ['Kinkaku-ji dan Gion', 'Kuil Emas pada pagi hari dan berjalan di distrik Gion saat sore.'],
                    ['Kembali ke Osaka', 'Belanja oleh-oleh dan diantar ke Bandara Kansai.'],
                ],
            ],
            [
                'destination' => 'Cappadocia',
                'title'       => 'Cappadocia Balloon Escape 7 Hari',
                'description' => 'Tujuh hari di Turki dengan Cappadocia sebagai pusatnya: terbang dengan balon udara, menginap di hotel gua, dan mampir ke Istanbul di awal dan akhir perjalanan.',
                'price'       => 21500000,
                'days'        => 7,
                'quota'       => 10,
                'starts_in'   => 120,
                'itinerary'   => [
                    ['Tiba di Istanbul', 'Check-in hotel dan makan malam di tepi Bosphorus.'],
                    ['Hagia Sophia dan Grand Bazaar', 'Tur kota tua Istanbul bersama pemandu.'],
                    ['Terbang ke Kayseri', 'Perjalanan menuju Göreme dan menikmati matahari terbenam di Love Valley.'],
                    ['Balon udara', 'Terbang dengan balon udara saat matahari terbit, lalu Red Tour di siang hari.'],
                    ['Kota bawah tanah', 'Mengunjungi Kaymakli, Pigeon Valley, dan workshop keramik Avanos.'],
                    ['Kembali ke Istanbul', 'Penerbangan pagi dan waktu bebas di Istiklal.'],
                    ['Kepulangan', 'Diantar ke bandara.'],
                ],
            ],
            [
                // Contoh paket draft: tidak tampil di halaman publik.
                'destination' => 'Lombok',
                'title'       => 'Lombok Rinjani Trekking 3 Hari',
                'description' => 'Pendakian Gunung Rinjani via Sembalun untuk pendaki yang sudah terbiasa trekking. Paket ini masih draf.',
                'price'       => 3200000,
                'days'        => 3,
                'quota'       => 10,
                'starts_in'   => 45,
                'published'   => false,
                'itinerary'   => [
                    ['Sembalun ke Plawangan', 'Pendakian menuju pos Plawangan Sembalun dan bermalam di tenda.'],
                    ['Puncak Rinjani', 'Summit sebelum matahari terbit, lalu turun ke Danau Segara Anak.'],
                    ['Turun via Senaru', 'Perjalanan turun dan diantar kembali ke hotel.'],
                ],
            ],
        ];
    }
}
