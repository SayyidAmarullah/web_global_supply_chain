<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@globalchain.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'company_name' => 'Global Chain HQ',
            'company_address' => '100 Enterprise Way',
            'country' => 'United States',
            'city' => 'New York',
            'phone_number' => '+15550100',
        ]);

        User::create([
            'name' => 'Logistics Manager',
            'email' => 'user@globalchain.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        $this->call([
            PortSeeder::class,
        ]);
    }
}
