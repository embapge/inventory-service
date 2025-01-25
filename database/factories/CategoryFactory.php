<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

class CategoryFactory extends Factory
{
    protected $model = Category::class;
    public function definition(): array
    {
        $faker = Faker::create("id_ID");

        return [
            "name" => $faker->bothify("??????????"),
            "description" => $faker->randomElement([null, $faker->text(50)]),
        ];
    }
}
