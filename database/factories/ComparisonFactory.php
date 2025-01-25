<?php

namespace Database\Factories;

use App\Models\Comparison;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

class ComparisonFactory extends Factory
{
    protected $model = Comparison::class;
    public function definition(): array
    {
        $faker = Faker::create("id_ID");
        return [
            "number_display" => $faker->bothify("#####/PRCHS/CMPR/2024"),
            "order_date" => $faker->dateTimeThisYear()
        ];
    }
}
