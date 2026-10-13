<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Support\Str;

trait GeneratesUniqueSlug
{
    /**
     * Buat slug unik untuk sebuah model.
     *
     * @param class-string<\Illuminate\Database\Eloquent\Model> $modelClass
     */
    protected function uniqueSlug(string $modelClass, string $name, ?int $ignoreId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;

        while (
            $modelClass::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}