<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;

class Media
{
    /**
     * Dapatkan URL publik untuk file yang tersimpan di disk public.
     */
    public static function url(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Storage::disk('public')->url($path);
    }

    /**
     * Periksa apakah file ada di disk public.
     */
    public static function exists(?string $path): bool
    {
        if (! $path) {
            return false;
        }

        return Storage::disk('public')->exists($path);
    }
}
