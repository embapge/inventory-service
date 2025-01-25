<?php

namespace Database\Factories;

use App\Enums\PurchaseOrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    public function definition(): array
    {
        $faker = Faker::create("id_ID");

        return [
            "order_date" => $faker->dateTimeThisYear(),
            "total_amount" => 0,
            "status" => PurchaseOrderStatus::PENDING,
        ];
    }
}
