<?php

namespace App\Services;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Dtos\StockMovementUpdateData;
use App\Dtos\StockMovementCreateData;
use App\Dtos\StockMovementProductData;
use App\Dtos\StockMovementProgressData;
use App\Enums\PurchaseOrderStatus;
use App\Enums\StockMovementStatus;
use App\Enums\StockMovementType;
use App\Models\StockMovement;
use App\Repositories\ProductRepository;
use App\Repositories\StockMovementRepository;
use App\Repositories\PurchaseOrderRepository;
use App\Repositories\StockRepository;
use App\Repositories\WarehouseRepository;
use App\Repositories\ShelfRepository;
use App\Repositories\StockMovementDetailRepository;
use App\Resources\StockMovementResource;
use Spatie\LaravelData\PaginatedDataCollection;

class StockMovementService
{
    public function __construct(protected StockMovementRepository $stockMovementRepository, protected StockMovementDetailRepository $stockMovementDetailRepository, protected StockRepository $stockRepository, protected WarehouseService $warehouseService, protected ShelfRepository $shelfRepository, protected ProductRepository $productRepository, protected PurchaseOrderRepository $purchaseOrderRepository) {}

    public function getPaginated(int $limit = 15)
    {
        return StockMovementResource::collect($this->stockMovementRepository->paginatedWithRelationships($limit), PaginatedDataCollection::class);
    }

    public function show(string $stockMovementId)
    {
        return StockMovementResource::from($this->stockMovementRepository->findByIdWithAllRelation($stockMovementId));
    }

    public function create(StockMovementCreateData $movementData)
    {
        $stock = DB::transaction(function () use ($movementData) {
            $destination = $this->warehouseService->show($movementData->warehouse_id);
            if ($movementData->stockable_type == "warehouse") {
                $source = $this->warehouseService->show($movementData->stockable_id);
                $productSources = $movementData->products;
                $warehouseId = $source->id;
            } else {
                if ($movementData->type == StockMovementType::OUTBOUND) {
                    throw new \InvalidArgumentException("Outbond only for warehouse to warehouse", 422);
                }

                if (!$source = $this->purchaseOrderRepository->findById($movementData->stockable_id, ["details"])) {
                    throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Purchase order with ud (" . $movementData->stockable_id . ") not found");
                }

                if ($source->status != PurchaseOrderStatus::APPROVED && $source->status != PurchaseOrderStatus::PAID && $source->status != PurchaseOrderStatus::DONE) {
                    throw new \InvalidArgumentException("Purchase order status must be approved, paid or done", 422);
                }

                $productSources = collect(StockMovementProductData::collect($source->details->map(fn($detail) => ["id" => $detail["product"]["id"], "qty" => $detail["qty"]])));
            }

            $Productduplicates = $productSources->duplicates("id");

            if ($Productduplicates->isNotEmpty()) {
                throw new \InvalidArgumentException("Product (" . $Productduplicates->join(", ") . ") cannot be duplicate. Please increase quantity", 422);
            }

            $products = $this->productRepository->getById($productSources->pluck("id")->toArray(), ["category"]);

            $unexistProducts = $productSources->whereNotIn("id", $products->pluck("id"));

            if ($unexistProducts->isNotEmpty()) {
                throw new \Illuminate\Support\ItemNotFoundException("Product with id (" . $unexistProducts->pluck("id")->join(", ") . ") not found", 404);
            }

            // Inbound
            if ($movementData->stockable_type == "warehouse" && ($movementData->type == StockMovementType::INBOUND || $movementData->type == StockMovementType::OUTBOUND)) {
                $stocks = $this->stockRepository->getStockByWarehouseIdProductIds($source->id, $products->pluck("id")->toArray());

                $unexistProductStock = $productSources->filter(fn($product) => $stocks->where("shelf.category_id", $products->where("id", $product->id)->first()->category_id)->where("product_id", $product->id)->where("qty", ">=", $product->qty)->isEmpty());

                if ($unexistProductStock->isNotEmpty()) {
                    throw new \Illuminate\Support\ItemNotFoundException("No exist or enough stocks in warehouse : " . $source->id . " and products " . $unexistProductStock->pluck("id")->join(", "), 404);
                }
            }
            // End Inbound

            $stock = $this->stockMovementRepository->create(array_merge($movementData->only("stockable_id", "stockable_type", "movement_date", "warehouse_id", "type")->toArray(), [
                "status" => [[
                    "name" => $movementData->status,
                    "datetime" => $movementData->datettime,
                    "reason" => $movementData->reason
                ]]
            ]), collect($productSources->toArray())->map(fn($product) => ["qty" => $product["qty"], "product_id" => $product["id"], "logs" => "[]"])->toArray());

            return $stock;
        });

        return StockMovementResource::from($stock);
    }

    public function update(StockMovementUpdateData $movementUpdateData)
    {
        if (!$movementUpdateData->stockMovement) {
            if (!$stockMovement = $this->stockMovementRepository->findById($movementUpdateData->stock_movement_id)) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Stock movement with id $movementUpdateData->stock_movement_id cannot be found.");
            }
        } elseif (!($movementUpdateData->stockMovement instanceof \App\Models\StockMovement)) {
            throw new \InvalidArgumentException("Stock movement must be instaceof stock movement model", 422);
        } else {
            $stockMovement = $movementUpdateData->stockMovement;
        }

        if ($stockMovement->status->last()['name'] != StockMovementStatus::LOADING->value) {
            throw new \InvalidArgumentException("Stock movement status must be loading.", 422);
        }

        $this->stockMovementRepository->update($stockMovement, $movementUpdateData->only("movement_date", "type")->toArray());
        return true;
    }

    public function destroy(string $stockMovementId)
    {
        return $this->stockMovementRepository->delete($stockMovementId);
    }

    public function updateStatus(StockMovementProgressData $stockMovementProgressData)
    {
        $stockMovement = $this->findStockMovementWithException($stockMovementProgressData->stock_movement_id);
        $this->stockMovementRepository->update($stockMovement, ["status" => $stockMovement->status->push(["reason" => $stockMovementProgressData->reason, "name" => $stockMovementProgressData->status, "datetime" => CarbonImmutable::now()])]);

        return true;
    }

    public function insertProduct(string $stockMovementId, Collection $stockMovementProgressDatas)
    {
        if (!$stockMovement = $this->stockMovementRepository->findById($stockMovementId)) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Stock movement with id $stockMovementId cannot be found.");
        }

        $shelfs = $this->validateInsertRemoveProductAndGetShelf($stockMovement->type, $stockMovement, $stockMovementProgressDatas);

        DB::transaction(function () use ($stockMovement, $shelfs, $stockMovementProgressDatas) {
            foreach ($shelfs as $shelf) {
                $stockMovementByShelfs = $stockMovementProgressDatas->where("shelf_id", $shelf->id);

                $this->shelfRepository->updateCapacity($shelf, $shelf->capacity - $stockMovementByShelfs->sum("qty"));

                foreach ($stockMovementByShelfs as $stockMovementByShelf) {
                    if ($productShelf = $shelf->products->where("id", $stockMovementByShelf->product_id)->first()) {
                        $this->stockRepository->updateQtyByShelf($shelf, $stockMovementByShelf->product_id, $productShelf->pivot->qty + $stockMovementByShelf->qty);
                    } else {
                        $this->stockRepository->create($shelf, $stockMovementByShelf->product_id, $stockMovementByShelf->qty);
                    }

                    $this->stockMovementDetailRepository->updateLog($stockMovement->details->where("product_id", $stockMovementByShelf->product_id)->first(), [
                        "id" => Str::uuid(),
                        "type" => $stockMovement->type,
                        "qty" => $stockMovementByShelf->qty,
                        "datetime" => CarbonImmutable::now()
                    ]);
                }
            }
        });

        return true;
    }

    public function removeProduct(string $stockMovementId, Collection $stockMovementProgressDatas)
    {
        if (!$stockMovement = $this->stockMovementRepository->findById($stockMovementId)) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Stock movement with id $stockMovementId cannot be found.");
        }

        $shelfs = $this->validateInsertRemoveProductAndGetShelf($stockMovement->type, $stockMovement, $stockMovementProgressDatas);

        DB::transaction(function () use ($stockMovement, $shelfs, $stockMovementProgressDatas) {
            foreach ($shelfs as $shelf) {
                $stockMovementByShelfs = $stockMovementProgressDatas->where("shelf_id", $shelf->id);

                $this->shelfRepository->updateCapacity($shelf, $shelf->capacity + $stockMovementByShelfs->sum("qty"));

                foreach ($stockMovementByShelfs as $stockMovementByShelf) {
                    $productShelf = $shelf->products->where("id", $stockMovementByShelf->product_id)->first();
                    $this->stockRepository->updateQtyByShelf($shelf, $stockMovementByShelf->product_id, $productShelf->pivot->qty - $stockMovementByShelf->qty);
                    $this->stockMovementDetailRepository->updateLog($stockMovement->details->where("product_id", $stockMovementByShelf->product_id)->first(), [
                        "id" => Str::uuid(),
                        "type" => $stockMovement->type,
                        "qty" => $stockMovementByShelf->qty,
                        "datetime" => CarbonImmutable::now()
                    ]);
                }
            }
        });

        return true;
    }

    public function findStockMovementWithException($id): StockMovement
    {
        if (!$stockMovement = $this->stockMovementRepository->findById($id)) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Stock movement with id " . $id . " not found.");
        }

        if ($stockMovement->lastStatus()["name"] == StockMovementStatus::DONE->value) {
            throw new \InvalidArgumentException("Status already done.", 422);
        }

        return $stockMovement;
    }

    public function validateInsertRemoveProductAndGetShelf(StockMovementType $type, StockMovement $stockMovement, Collection $stockMovementProgressDatas)
    {
        if ($type == StockMovementType::OUTBOUND && $stockMovement->lastStatus()["name"] != StockMovementStatus::APPROVED->value) {
            throw new \InvalidArgumentException("Stock movement must be approved", 422);
        }else if($type == StockMovementType::INBOUND && $stockMovement->lastStatus()["name"] != StockMovementStatus::PROCESS->value){
            throw new \InvalidArgumentException("Stock movement must be process", 422);
        }

        $stockMovement->load(["details"]);

        if ($stockMovementProgressDatas->filter(fn($progress) => !($progress instanceof \App\Dtos\StockMovementAddRemoveShelfData))->isNotEmpty()) {
            throw new \InvalidArgumentException("Data must be instanceof stock movement progress data.", 422);
        }

        $unexistProducts = $stockMovementProgressDatas->filter(fn($detail) => !in_array($detail->product_id, $stockMovement->details->pluck("product_id")->toArray()));

        if ($unexistProducts->isNotEmpty()) {
            throw new \Illuminate\Support\ItemNotFoundException("Stock movement (" . $stockMovement->id . ") doesn't have product with id: " . $unexistProducts->pluck("product_id")->join(", "), 404);
        }

        if (!$products = $this->productRepository->getById($stockMovementProgressDatas->pluck("product_id")->toArray())) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("There's not exist for all products given.");
        }

        $unAvailableProduct = $stockMovementProgressDatas->whereNotIn("product_id", $products->pluck("id")->toArray());

        if ($unAvailableProduct->isNotEmpty()) {
            throw new \Illuminate\Support\ItemNotFoundException("Product with id: " . $unAvailableProduct->pluck("product_id")->join(", ") . " not found.");
        }

        $unmatchedQty = $stockMovement->details->whereIn("id", $stockMovementProgressDatas->pluck("product_id")->toArray())->filter(fn($detail) => $detail->logs->sum("qty") >= $stockMovementProgressDatas->where("product_id", $detail->product_id)->sum("qty"));

        if ($unmatchedQty->isNotEmpty()) {
            throw new \InvalidArgumentException("Qty product_id: " . $unmatchedQty->pluck("product_id")->join(", ") . " is bigger than is should.", 422);
        }

        if ($type == StockMovementType::INBOUND) {
            if (!$shelfs = $this->shelfRepository->getByWarehouseAvailableCapacityWithProductsAndId($stockMovement->warehouse_id, $stockMovementProgressDatas->pluck("shelf_id")->toArray(), $stockMovement->details->pluck("product_id")->toArray())) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("There's not enough or exist for all shelf.");
            }

            $unAvailableShelf = $stockMovementProgressDatas->whereNotIn("shelf_id", $shelfs->pluck("id")->toArray());

            if ($unAvailableShelf->isNotEmpty()) {
                throw new \Illuminate\Support\ItemNotFoundException("Shelf with id: " . $unAvailableShelf->pluck("shelf_id")->join(", ") . " not found or not enough capacity.");
            }

            $productDifferentCategories = $stockMovementProgressDatas->filter(fn($stockMovementProgressData) => $shelfs->where("id", $stockMovementProgressData->shelf_id)->first()->category_id != $products->where("id", $stockMovementProgressData->product_id)->first()->category_id);

            if ($productDifferentCategories->isNotEmpty()) {
                throw new \InvalidArgumentException("Different shelf category with id: " . $productDifferentCategories->pluck("shelf_id")->join(", "), 422);
            }

            $shelfLessCapacity = $shelfs->filter(fn($shelf) => $shelf->capacity < $stockMovementProgressDatas->where("shelf_id", $shelf->id)->sum("qty"));

            if ($shelfLessCapacity->isNotEmpty()) {
                throw new \InvalidArgumentException("(" . $shelfLessCapacity->pluck("id")->join(", ") . ") doesn't have enough capacity for related product. Please adjust product qty to another shelfs", 422);
            }

            return $shelfs;
        } elseif ($type == StockMovementType::OUTBOUND) {
            $stocks = $this->stockRepository->getStockByWarehouseIdProductIds($stockMovement->stockable_id, $products->pluck("id")->toArray());

            $unexistProductStock = $stockMovementProgressDatas->filter(fn($stockMovementProgressData) => $stocks->where("shelf_id", $stockMovementProgressData->shelf_id)->where("product_id", $stockMovementProgressData->product_id)->where("qty", ">=", $stockMovementProgressData->qty)->isEmpty());

            if ($unexistProductStock->isNotEmpty()) {
                throw new \Illuminate\Support\ItemNotFoundException("No exist or enough stocks in warehouse : " . $stockMovement->stockable_id . " and products " . $unexistProductStock->pluck("id")->join(", "), 404);
            }

            return $stocks->pluck("shelf");
        }
    }
}
