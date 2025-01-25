<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

class SupplierFactory extends Factory
{
    public function definition(): array
    {
        $faker = Faker::create("id_ID");
        return [
            "name" => $faker->name(),
            "phone" => [$faker->phoneNumber(), $faker->phoneNumber()],
            "email" => [$faker->safeEmail(), $faker->safeEmail()],
            "address" => $faker->address(),
        ];
    }
}
