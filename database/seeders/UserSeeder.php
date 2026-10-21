<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Akun demo, hanya untuk lokal/staging.
     * Ganti password di array ini sesuai keinginan.
     */
    public function run(): void
    {
        // Seeder ini tidak boleh membuat akun dengan password yang tertulis di kode
        // saat berjalan di production.
        if (app()->isProduction()) {
            $this->command?->warn('UserSeeder dilewati di production. Buat admin secara manual.');
            return;
        }

        $this->createUser('Administrator',  'admin@gmail.com',     '081200000001', UserRole::ADMIN,      'admin123');
        $this->createUser('Sari Wulandari', 'tourguide@gmail.com', '081200000002', UserRole::TOUR_GUIDE, 'guide123');
        $managerUser = $this->createUser('Made Wirawan', 'manager@gmail.com', '081200000003', UserRole::MANAGER, 'manager123');
        $this->createUser('Budi Santoso',   'user@example.com',    '081200000004', UserRole::USER,       'User#12345');
        $this->createUser('Rina Anggraini', 'rina@example.com',    '081200000005', UserRole::USER,       'User#12345');

        Vendor::firstOrCreate(
            ['user_id' => $managerUser->id],
            [
                'company_name' => 'Bali Jaya Wisata',
                'address'      => 'Jl. Sunset Road No. 88, Kuta, Bali',
                'status'       => 'active',
            ]
        );
    }

    private function createUser(string $name, string $email, string $phone, UserRole $role, string $password): User
    {
        $user = User::firstOrNew(['email' => $email]);

        $user->forceFill([
            'name'              => $name,
            'phone'             => $phone,
            'role'              => $role,
            'password'          => Hash::make($password), // bcrypt
            'is_active'         => true,
            'email_verified_at' => now(),
        ])->save();

        return $user;
    }
}