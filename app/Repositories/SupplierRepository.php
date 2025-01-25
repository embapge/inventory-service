<?php

namespace App\Repositories;

use App\Models\Supplier;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class SupplierRepository
{
    public function paginatedWithRelationships(int $limit)
    {
        $supplier = new Supplier();
        $supplier = validateEagerLoadRelation($supplier, new Supplier());
        return $supplier->paginate($limit);
    }

    public function show(string $id, array $relationships = [])
    {
        $supplier =  Supplier::where("id", $id);
        $supplier = validateEagerLoadRelation($supplier, new Supplier(), $relationships);
        return $supplier->first();
    }

    public function findById(string $id, array $relationships = []): Supplier
    {
        return Supplier::with($relationships)->where("id", $id)->first();
    }

    public function getById(array $ids, array $relationships = []): Collection
    {
        return Supplier::with($relationships)->whereIn("id", $ids)->get();
    }

    public function create(array $data): Supplier
    {
        return Supplier::create($data)->fresh();
    }

    public function update(string $id, array $data): bool
    {
        if (!$supplier = Supplier::find($id)) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Supplier with id $id not found.");
        }

        return $supplier->update($data);
    }

    public function delete(string $id): bool
    {
        try {
            if (!$supplier = Supplier::find($id)) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Supplier with id $id not found.");
            }

            return $supplier->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                throw new \App\Exceptions\ForeignKeyConstraintException("Cannot delete Supplier ($id) record because it is referenced in other records.");
            }

            Log::error('QueryException encountered', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'code' => $e->getCode(),
            ]);

            throw new \Exception("Failed to delete supplier", 500);
        }
    }
}
