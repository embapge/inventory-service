<?php

namespace Database\Seeders;

use App\Models\Comparison;
use App\Models\ComparisonDetail;
use App\Models\PurchaseOrder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class ComparisonDetailSeeder extends Seeder
{
    public function run(): void
    {
        PurchaseOrder::with("comparisonDetails")->chunk(2, function (Collection $purchaseOrders) {
            foreach ($purchaseOrders as $purchaseOrder) {
                if ($purchaseOrder->comparisonDetails->isNotEmpty()) {
                    continue;
                }

                do {
                    $comparison = Comparison::withCount("details")->inRandomOrder()->first();
                } while ($comparison->details_count > 1);

                $comparison->details()->create([
                    "purchase_order_id" => $purchaseOrder->id
                ]);
            }
        });
    }
}
