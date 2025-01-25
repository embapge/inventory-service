<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            WarehouseSeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
            PurchaseOrderSeeder::class,
            ComparisonSeeder::class,
            ComparisonDetailSeeder::class,
        ]);
    }
}
