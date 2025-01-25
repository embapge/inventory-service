<?php

namespace App\Repositories;

use App\Models\Shelf;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class ShelfRepository
{
    public function getByWarehouseAvailableCapacityWithProductsAndId(string $warehouse_id, array $ids, array $productIds)
    {
        return Shelf::where("capacity", ">", 0)->with(["products" => function ($q) use ($productIds) {
            $q->where("id", $productIds);
        }])->whereIn("id", $ids)->where("warehouse_id", $warehouse_id)->get();
    }

    public function updateCapacity(Shelf $shelf, int $capacity)
    {
        return $shelf->update([
            "capacity" => $capacity
        ]);
    }

    public function paginatedWithRelationships(array $relationships = [], int $limit)
    {
        $shelf = new Shelf();
        $shelf = validateEagerLoadRelation($shelf, new Shelf(), $relationships);
        return $shelf->paginate($limit > 50 ? 50 : $limit);
    }

    public function findById(string $id)
    {
        return Shelf::find($id);
    }

    public function findByIdWithAllRelation(string $id)
    {
        return Shelf::with(["warehouse", "category"])->find($id);
    }

    public function lazyLoadAllRelation(Shelf $shelf)
    {
        return $shelf->load(["category", "warehouse"]);
    }

    public function create(array $data)
    {
        return Shelf::create($data)->fresh();
    }

    public function update(string $id, array $data): bool
    {
        if (!$shelf = Shelf::find($id)) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Shelf with id $id not found.");
        }

        return $shelf->update($data);
    }

    public function delete(string $id): bool
    {
        try {
            if (!$shelf = Shelf::find($id)) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Shelf with id $id not found.");
            }

            return $shelf->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                throw new \App\Exceptions\ForeignKeyConstraintException("Cannot delete Shelf ($id) record because it is referenced in other records.");
            }

            Log::error('QueryException encountered', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'code' => $e->getCode(),
            ]);

            throw new \Exception("Failed to delete shelf", 500);
        }
    }
}
