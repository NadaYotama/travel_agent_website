<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Vendor;
use Illuminate\Http\Request;

trait ResolvesVendor
{
    /**
     * Ambil profil vendor milik user yang login.
     * Dibuat otomatis jika admin baru membuat akun vendor tanpa profil.
     */
    protected function vendorOf(Request $request): Vendor
    {
        $user = $request->user();

        return Vendor::firstOrCreate(
            ['user_id' => $user->id],
            ['company_name' => $user->name, 'status' => 'active']
        );
    }
}