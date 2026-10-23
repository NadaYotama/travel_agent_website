<?php

namespace App\Support;

class DemoGallery
{
    public const HERO_TARGET = 'hero.jpg';

    /**
     * Peta slug destinasi ke nama file di storage disk public.
     */
    public static function destinationPath(string $slug): ?string
    {
        $map = [
            'bali'        => 'destinations/bali.jpg',
            'cappadocia'  => 'destinations/cappadocia.jpg',
            'kyoto'       => 'destinations/kyoto.jpg',
            'labuan-bajo' => 'destinations/labuan-bajo.jpg',
            'lombok'      => 'destinations/lombok.jpg',
            'raja-ampat'  => 'destinations/raja-ampat.jpg',
            'yogyakarta'  => 'destinations/yogyakarta.jpg',
        ];

        return $map[$slug] ?? null;
    }

    /**
     * Peta slug paket ke nama file di storage disk public.
     */
    public static function packagePath(string $slug): ?string
    {
        $map = [
            'bali-timur-4-hari-3-malam'            => 'packages/bali-timur-4-hari-3-malam.jpg',
            'cappadocia-balloon-escape-7-hari'     => 'packages/cappadocia-balloon-escape-7-hari.jpg',
            'kyoto-osaka-6-hari'                   => 'packages/kyoto-osaka-6-hari.jpg',
            'lombok-rinjani-trekking-3-hari'       => 'packages/lombok-rinjani-trekking-3-hari.jpg',
            'open-trip-labuan-bajo-3d2n'           => 'packages/open-trip-labuan-bajo-3d2n.jpg',
            'raja-ampat-island-hopping-5d4n'       => 'packages/raja-ampat-island-hopping-5d4n.jpg',
            'yogyakarta-heritage-3-hari'           => 'packages/yogyakarta-heritage-3-hari.jpg',
        ];

        return $map[$slug] ?? null;
    }
}
