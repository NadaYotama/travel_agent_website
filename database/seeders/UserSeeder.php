<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UserSeeder extends Seeder
{
    /**
     * Akun yang dibuat (kata sandi default: "password", ubah lewat SEED_PASSWORD di .env):
     *
     *   admin@example.com   -> Admin
     *   agent@example.com   -> Agent
     *   vendor@example.com  -> Vendor (lengkap dengan profil vendor)
     *   user@example.com    -> Customer
     *   rina@example.com    -> Customer
     *
     * Seeder aman dijalankan berulang: akun yang sudah ada tidak diubah.
     */
    public function run(): void
    {
        $password = $this->password();

        // Admin selalu dibuat, termasuk di production.
        $this->createUser([
            'name'  => 'Administrator',
            'email' => env('SEED_ADMIN_EMAIL', 'admin@example.com'),
            'phone' => '081200000001',
            'role'  => UserRole::ADMIN,
        ], $password);

        // Akun contoh hanya untuk lokal/staging.
        if (app()->isProduction()) {
            return;
        }

        $this->createUser([
            'name'  => 'Sari Wulandari',
            'email' => 'agent@example.com',
            'phone' => '081200000002',
            'role'  => UserRole::AGENT,
        ], $password);

        $vendorUser = $this->createUser([
            'name'  => 'Made Wirawan',
            'email' => 'vendor@example.com',
            'phone' => '081200000003',
            'role'  => UserRole::VENDOR,
        ], $password);

        Vendor::firstOrCreate(
            ['user_id' => $vendorUser->id],
            [
                'company_name' => 'Bali Jaya Wisata',
                'address'      => 'Jl. Sunset Road No. 88, Kuta, Bali',
                'status'       => 'active',
            ]
        );

        $this->createUser([
            'name'  => 'Budi Santoso',
            'email' => 'user@example.com',
            'phone' => '081200000004',
            'role'  => UserRole::USER,
        ], $password);

        $this->createUser([
            'name'  => 'Rina Anggraini',
            'email' => 'rina@example.com',
            'phone' => '081200000005',
            'role'  => UserRole::USER,
        ], $password);
    }

    /** Buat user hanya jika belum ada, sehingga kata sandi yang sudah diganti tidak tertimpa. */
    private function createUser(array $attributes, string $password): User
    {
        $user = User::firstOrNew(['email' => $attributes['email']]);

        if (! $user->exists) {
            $user->forceFill($attributes + [
                'password'          => Hash::make($password),
                'is_active'         => true,
                'email_verified_at' => now(),
            ])->save();
        }

        return $user;
    }

    private function password(): string
    {
        $password = env('SEED_PASSWORD');

        if (app()->isProduction() && empty($password)) {
            throw new RuntimeException(
                'Isi SEED_PASSWORD di .env sebelum menjalankan seeder di production, ' .
                'agar akun admin tidak memakai kata sandi default.'
            );
        }

        return $password ?: 'password';
    }
}