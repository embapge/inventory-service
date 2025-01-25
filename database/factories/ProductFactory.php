<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;
use App\Models\Product;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;
    public function definition(): array
    {
        $faker = Faker::create("id_ID");

        return [
            "name" => $faker->bothify("??????????"),
            "price" => $faker->bothify("##########"),
            "is_active" => $faker->randomElement([0, 1]),
            "description" => $faker->randomElement([null, $faker->text(50)]),
            "reorder_level" => $faker->randomElement(["low", "medium", "high"]),
        ];
    }
}
