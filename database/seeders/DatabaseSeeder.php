<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        $admin = Admin::create(
            [
                'name' => 'Admin User',
                'email' => 'admin@domain.com',
                'password' => Hash::make('12345678'),
                'status' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(10)
            ]
        );

        Role::create(['name' => 'super_admin', 'guard_name' => 'admin']);

        $admin->assignRole('super_admin');
    }
}
