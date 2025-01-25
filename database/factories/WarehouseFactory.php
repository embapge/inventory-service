<?php

namespace Database\Factories;

use App\Enums\WarehouseType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Warehouse>
 */
class WarehouseFactory extends Factory
{
    public function definition(): array
    {
        $faker = Faker::create("id_ID");
        return [
            "name" => $faker->name(),
            "type" => $faker->RandomElement(array_column(WarehouseType::cases(), 'value')),
            "location" => $faker->address(),
            "lat" => $faker->latitude(),
            "lang" => $faker->longitude(),
            "capacity" => $faker->bothify("####"),
        ];
    }
}
