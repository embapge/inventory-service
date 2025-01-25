<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Shelf;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

class ShelfFactory extends Factory
{
    public function definition(): array
    {
        $faker = Faker::create("id_ID");

        do {
            $number = $faker->bothify("??????????");
        } while (Shelf::firstWhere(["code" => $number]));

        return [
            "code" => $number,
            "capacity" => $faker->bothify("##"),
            "category_id" => Category::inRandomOrder()->first()->id
        ];
    }
}
