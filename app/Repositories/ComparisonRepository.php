<?php

namespace App\Repositories;

use App\Models\Comparison;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class ComparisonRepository
{
    public function paginatedWithRelationships(int $limit = 15): LengthAwarePaginator
    {
        $query = Comparison::with(["details.purchaseOrder.supplier"]);
        return $query->paginate($limit > 20 ? 20 : $limit);
    }

    public function findById(string $id, array $relationships = []): Comparison
    {
        return Comparison::with($relationships)->where("id", $id)->first();
    }

    public function create(array $data)
    {
        return Comparison::create($data);
    }

    public function update(string $id, array $data)
    {
        if (!$comparison = Comparison::find($id)) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Comparison with id $id not found.");
        }

        return $comparison->update($data);
    }

    public function loadAllRelation(Comparison $comparison)
    {
        return $comparison->fresh()->load(["details.purchaseOrder" => ["supplier", "details"]]);
    }

    public function delete(string $id)
    {
        try {
            if (!$comparison = Comparison::find($id)) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Comparison with id $id not found.");
            }

            return $comparison->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                throw new \App\Exceptions\ForeignKeyConstraintException("Cannot delete comparison ($id) record because it is referenced in other records.");
            }

            Log::error('QueryException encountered', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'code' => $e->getCode(),
            ]);

            throw new \Exception("Failed to delete comparison", 500);
        }
    }

    public function findLastNumberDisplay()
    {
        return Comparison::whereNotNull("number_display")->whereNot("number_display", "")->where("number_display", "like", "%/" . now()->format("Y"))->orderByDesc("number_display")?->first()?->number_display;
    }
}
