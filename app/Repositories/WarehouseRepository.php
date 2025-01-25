<?php

namespace App\Repositories;

use App\Models\Warehouse;
use App\Resources\PurchaseOrderResource;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class WarehouseRepository
{
    public function paginated(int $limit)
    {
        $warehouse = new Warehouse();
        return $warehouse->paginate($limit > 50 ? 50 : $limit);
    }

    public function findById(string $id, array $relationships = [])
    {
        $warehouse =  Warehouse::where("id", $id);
        $warehouse = validateEagerLoadRelation($warehouse, new Warehouse(), $relationships);
        return $warehouse->first();
    }

    public function findByIdAndCategories(string $id, array $categoryIds)
    {
        $warehouse =  Warehouse::where("id", $id)->withWhereHas("shelfs", function ($q) use ($categoryIds) {
            $q->whereIn("category_id", $categoryIds);
        });
        return $warehouse->first();
    }

    public function getById(array $ids, array $relationships = []): Collection
    {
        return Warehouse::with($relationships)->whereIn("id", $ids)->get();
    }

    public function create(array $data): Warehouse
    {
        return Warehouse::create($data)->fresh();
    }

    public function update(string $id, array $data): bool
    {
        if (!$warehouse = Warehouse::find($id)) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Warehouse with id $id not found.");
        }

        return $warehouse->update($data);
    }

    public function delete(string $id): bool
    {
        try {
            if (!$warehouse = Warehouse::find($id)) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Warehouse with id $id not found.");
            }

            return $warehouse->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                throw new \App\Exceptions\ForeignKeyConstraintException("Cannot delete Warehouse ($id) record because it is referenced in other records.");
            }

            Log::error('QueryException encountered', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'code' => $e->getCode(),
            ]);

            throw new \Exception("Failed to delete warehouse", 500);
        }
    }
}
