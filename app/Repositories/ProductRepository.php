<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\PurchaseOrder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ProductRepository
{
    public function paginated(int $limit)
    {
        return Product::paginate($limit > 50 ? 100 : $limit);
    }

    public function findById(string $id, array $relationships = [])
    {
        $product =  Product::where("id", $id);
        $product = validateEagerLoadRelation($product, new Product(), $relationships);
        return $product->first();
    }

    public function getById(array $ids, array $relationships = [])
    {
        $product =  Product::whereIn("id", $ids);
        $product = validateEagerLoadRelation($product, new Product(), $relationships);
        return $product->get();
    }

    public function create(array $data)
    {
        return Product::create($data)->fresh();
    }

    public function update(string $id, array $data)
    {
        if (!$product = Product::find($id)) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Product with id $id not found.");
        }

        return $product->update($data);
    }

    public function delete(string $id)
    {
        try {
            if (!$product = Product::find($id)) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Product with id $id not found.");
            }

            return $product->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                throw new \App\Exceptions\ForeignKeyConstraintException("Cannot delete product ($id) record because it is referenced in other records.");
            }

            Log::error('QueryException encountered', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'code' => $e->getCode(),
            ]);

            throw new \Exception("Failed to delete product", 500);
        }
    }

    public function getByWarehouseWithShelf(string $warehouse_id, array $productIds)
    {
        return Product::with(["shelf" => function ($q) use ($warehouse_id) {
            $q->whereHas("warehouse", function ($q) use ($warehouse_id) {
                $q->where("id", $warehouse_id);
            });
        }])->whereIn("id", $productIds)->get();
    }
}
