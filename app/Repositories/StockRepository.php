<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Collection;
use App\Models\Stock;
use App\Dtos\ProductData as StockMovementProductData;
use App\Models\Shelf;
use App\Models\Warehouse;

class StockRepository
{
    public function getStockByWarehouseIdProductIds(string $warehouseId, array $productIds): Collection
    {
        return Stock::withWhereHas("shelf", function ($q) use ($warehouseId) {
            $q->whereHas("warehouse", function ($q) use ($warehouseId) {
                $q->where("id", $warehouseId);
            });
        })->withWhereHas("product", function ($q) use ($productIds) {
            $q->whereIn("id", $productIds);
        })->get();
    }

    public function create(Shelf $shelf, string $product_id, int $qty)
    {
        return $shelf->products()->attach($product_id, ["qty" => $qty]);
    }

    public function updateQtyByShelf(Shelf $shelf, string $product_id, int $qty)
    {
        return $shelf->products()->updateExistingPivot($product_id, ["qty" => $qty]);
    }

    public function updateQtyByProduct(Product $product, string $shelf_id, int $qty)
    {
        return $product->shelfs()->updateExistingPivot($shelf_id, ["qty" => $qty]);
    }
}
