<?php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@shahadat.com'],
            [
                'name'              => 'Admin',
                'password'          => Hash::make('admin#password'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'sayem@symbahar.com'],
            [
                'name'              => 'Sayem',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'employee@symbahar.com'],
            [
                'name'              => 'Employee',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
    }
}
