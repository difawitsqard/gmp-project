<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\User;
use App\Models\Product;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ProductCategory;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $Units = [
            ['name' => 'Buah', 'short_name' => 'BH', 'status' => true],
            ['name' => 'Batang', 'short_name' => 'BT', 'status' => true],
            ['name' => 'Roll', 'short_name' => null, 'status' => true],
        ];
        foreach ($Units as $unit) {
            Unit::create($unit);
        }

        $ProductCategories = [
            [
                'name' => 'VALVE',
                'slug' => 'valve',
                'description' => 'Valve',
                'status' => true,
            ],
            [
                'name' => 'PIPA',
                'slug' => 'pipa',
                'description' => 'Pipa',
                'status' => true,
            ],
            [
                'name' => 'KATUP',
                'slug' => 'katup',
                'description' => 'Katup',
                'status' => true,
            ],
            [
                'name' => 'UMUM',
                'slug' => 'umum',
                'description' => 'Umum',
                'status' => true,
            ],
            [
                'name' => 'PLAT',
                'slug' => 'plat',
                'description' => 'Plat',
                'status' => true,
            ],
            [
                'name' => 'FITTING',
                'slug' => 'fitting',
                'description' => 'Fitting',
                'status' => true,
            ],
        ];
        foreach ($ProductCategories as $category) {
            ProductCategory::create($category);
        }

        ProductCategory::factory(50)->create();

        Product::factory(30)->recycle([
            ProductCategory::all(),
            Unit::all(),
        ])->create();
    }
}
