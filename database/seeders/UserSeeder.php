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
        $roles = Role::pluck('name');

        foreach ($roles as $roleName) {
            // Buat user baru
            $user = User::factory()->create([
                'name' => $roleName . ' User',
                'email' => strtolower(str_replace(' ', '', $roleName)) . '@example.com',
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
            ]);
            $user->assignRole($userData['role']);
        }

        // factory
        User::factory()->count(18)->create()->each(function ($user) {
            $user->assignRole('Pelanggan');
        });
    }
}
