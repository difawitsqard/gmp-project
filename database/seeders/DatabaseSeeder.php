<?php

namespace Database\Seeders;

use App\Models\Tax;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $taxes = [
            [
                'name' => 'PPN',
                'percent' => 10,
                'fixed_amount' => null,
            ],
            [
                'name' => 'Biaya Layanan',
                'percent' => null,
                'fixed_amount' => 5000,
            ],
        ];

        foreach ($taxes as $tax) {
            Tax::create($tax);
        }

        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            UnitSeeder::class,
            ProductCategorySeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
            OrderSeeder2::class,
        ]);
    }
}
