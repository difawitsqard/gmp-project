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
                'email' => strtolower($roleName) . '@example.com',
            ]);

            // Assign role ke user
            $user->assignRole($roleName);
        }
    }
}
