<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // First SuperAdmin account. CHANGE THIS PASSWORD after first login.
        User::firstOrCreate(
            ['username' => 'superadmin'],
            [
                'password' => Hash::make('ChangeMe123!'),
                'role' => 'SUPERADMIN',
                'customer_id' => null,
            ]
        );
    }
}
