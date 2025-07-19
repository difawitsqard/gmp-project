<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'Admin',
            'Staff Gudang',
            'Manajer Stok',
            'Pelanggan',
        ];

        foreach ($roles as $roleName) {
            Role::create(['name' => $roleName]);
        }
    }
}
