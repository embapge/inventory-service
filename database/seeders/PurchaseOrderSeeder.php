<?php

namespace Database\Seeders;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\PurchaseOrderService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PurchaseOrderSeeder extends Seeder
{
    public function __construct(protected PurchaseOrderService $purchaseOrderService) {}
    public function run(): void
    {
        PurchaseOrder::factory()->count(1)->for(Supplier::inRandomOrder()->first())->hasDetails(8)->create();

        $purchaseOrders = PurchaseOrder::all();

        foreach ($purchaseOrders as $purchaseOrder) {
            $this->purchaseOrderService->setPurchaseOrder($purchaseOrder)->calculate();
        }
    }
}
