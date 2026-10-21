<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('role', 'agent')->update(['role' => 'tourguide']);
        DB::table('users')->where('role', 'vendor')->update(['role' => 'manager']);
    }

    public function down(): void
    {
        DB::table('users')->where('role', 'tourguide')->update(['role' => 'agent']);
        DB::table('users')->where('role', 'manager')->update(['role' => 'vendor']);
    }
};
