<?php

namespace Database\Factories;

use App\Dtos\ProductData;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Faker\Factory as Faker;

class PurchaseOrderDetailFactory extends Factory
{
    public function definition(): array
    {
        $faker = Faker::create("id_ID");
        $product = Product::with("category")->inRandomOrder()->first();
        return [
            "product" => array_merge($product->only("id", "category_id", "name", "price", "description", "is_active", "reorder_level"), ["category" => $product->category->only("id", "name", "description")]),
            "qty" => $faker->bothify("##"),
        ];
    }
}
