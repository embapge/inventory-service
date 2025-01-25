<?php

namespace App\Repositories;

use App\Models\PurchaseOrder;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\StockMovementDetail;
use App\Models\Warehouse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StockMovementRepository
{
    public function paginatedWithRelationships(int $limit = 15): LengthAwarePaginator
    {
        $query = StockMovement::with(["stockable" => function (MorphTo $morphTo) {
            $morphTo->morphWith([
                PurchaseOrder::class => ['detail'],
                Warehouse::class,
            ]);
        }, "warehouse", "details" => ["product.category"]]);

        if ($limit > 15) {
            $limit = 15;
        }

        return $query->paginate($limit);
    }

    public function findById($id, $relationship = []): StockMovement
    {
        return StockMovement::with($relationship)->where("id", $id)->first();
    }

    public function findByIdWithAllRelation(string $id)
    {
        return StockMovement::with(["stockable", "details.product"])->where("id", $id)->first();
    }

    public function loadAllRelation(StockMovement $stockMovement)
    {
        return $stockMovement->load(["stockable", "details.product"]);
    }

    public function create(array $data, array $products)
    {
        $stockMovement = StockMovement::create($data);
        StockMovementDetail::insert(collect($products)->map(fn($product) => array_merge($product, ["id" => Str::uuid(), "stock_movement_id" => $stockMovement->id]))->toArray());

        return $stockMovement->fresh()->load("details");
    }

    public function update(StockMovement $stockMovement, array $data)
    {
        return $stockMovement->update($data);
    }

    public function delete(string $id)
    {
        try {
            if (!$stockMovement = StockMovement::find($id)) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Stock movement with id $id not found.");
            }

            return $stockMovement->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                throw new \App\Exceptions\ForeignKeyConstraintException("Cannot delete stock movement ($id) record because it is referenced in other records.");
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
