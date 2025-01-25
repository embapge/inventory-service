<?php

namespace App\Repositories;

use App\Models\StockMovement;
use App\Models\StockMovementDetail;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class StockMovementDetailRepository
{
    public function updateLog(StockMovementDetail $detail, array $data)
    {
        return $detail->update(["logs" => $detail->logs->push($data)]);
    }

    public function findById(string $id, array $relationships = [])
    {
        return StockMovementDetail::with($relationships)->where("id", $id)->first();
    }

    public function create(array $data)
    {
        return StockMovementDetail::create($data)->fresh();
    }

    public function updateQtyUsingModel(StockMovementDetail $stockMovementDetail, int $qty)
    {
        return $stockMovementDetail->update([
            "qty" => $qty
        ]);
    }

    public function loadAllRelationship(StockMovementDetail $stockMovementDetail)
    {
        return $stockMovementDetail->load(["stockMovement", "products"]);
    }

    public function delete(string $id)
    {
        try {
            if (!$stockMovementDetail = StockMovementDetail::find($id)) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Stock movement detail with id $id not found.");
            }

            return $stockMovementDetail->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                throw new \App\Exceptions\ForeignKeyConstraintException("Cannot delete stock movement detail ($id) record because it is referenced in other records.");
            }

            Log::error('QueryException encountered', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'code' => $e->getCode(),
            ]);

            throw new \Exception("Failed to delete stock movement", 500);
        }
    }
}
