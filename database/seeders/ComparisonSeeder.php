<?php

namespace Database\Seeders;

use App\Models\Comparison;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComparisonSeeder extends Seeder
{
    public function run(): void
    {
        Comparison::factory()->count(1)->create();
    }
}
