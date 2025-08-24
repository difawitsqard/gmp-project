<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startDate = \Carbon\Carbon::create(2025, 7, 24, 0, 0, 0); // July 24, 2025 at 00:00:00
        $endDate = \Carbon\Carbon::create(2025, 7, 29, 0, 0, 0); // July 29, 2025 at 00:00:00

        $randomDateTime = \Carbon\Carbon::createFromTimestamp(
            rand($startDate->timestamp, $endDate->timestamp)
        );

        $roles = Role::pluck('name');

        foreach ($roles as $roleName) {
            // Buat user baru
            $user = User::factory()->create([
                'name' => $roleName . ' User',
                'email' => strtolower(str_replace(' ', '', $roleName)) . '@example.com',
                'created_at' => $randomDateTime,
                'updated_at' => $randomDateTime,
            ]);

            // Assign role ke user
            $user->assignRole($roleName);
        }

        // create user
        $users = [
            [
                'name' => 'Neni Nur Fitriani',
                'email' => 'neni.nf03@gmail.com',
                'password' => bcrypt('pass123'),
                'role' => 'Admin'
            ],
            [
                'name' => 'Hendri Trisnadi',
                'email' => 'hendritr1s@gmail.com',
                'password' => bcrypt('pass123'),
                'role' => 'Manajer Stok'
            ],
            [
                'name' => 'Debi Yulianti',
                'email' => 'debi.gmp17@gmail.com',
                'password' => bcrypt('pass123'),
                'role' => 'Staff Gudang'
            ],
        ];

        foreach ($users as $userData) {
            $user = User::factory()->create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $userData['password'],
                'phone' => null,
                'address' => null,
                'created_at' => $randomDateTime,
                'updated_at' => $randomDateTime,
            ]);
            $user->assignRole($userData['role']);
        }

        // factory
        User::factory()->count(18)->create()->each(function ($user) {
            $user->assignRole('Pelanggan');
        });
    }
}
