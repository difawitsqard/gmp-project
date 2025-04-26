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
            ['name' => 'VALVE'],
            ['name' => 'PIPA'],
            ['name' => 'KATUP'],
            ['name' => 'UMUM'],
            ['name' => 'PLAT'],
            ['name' => 'FITTING'],
        ];
        foreach ($ProductCategories as $category) {
            ProductCategory::create($category);
        }

        Product::factory(30)->recycle([
            ProductCategory::all(),
            Unit::all(),
        ])->create();
    }
}
