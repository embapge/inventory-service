<?php

namespace App\Services;

use App\Dtos\StockMovementDetailCreateData;
use App\Dtos\StockMovementDetailUpdateData;
use App\Enums\StockMovementStatus;
use App\Repositories\StockMovementDetailRepository;
use App\Repositories\StockMovementRepository;
use App\Resources\StockMovementDetailResource;

class StockMovementDetailService
{
    public function __construct(protected StockMovementDetailRepository $stockMovementDetailRepository) {}

    public function create(StockMovementDetailCreateData $stockMovementDetailCreateData)
    {
        if (!$stockMovement = (new StockMovementRepository)->findById($stockMovementDetailCreateData->stock_movement_id)) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Stock movement ($stockMovementDetailCreateData->stock_movement_id) not found.");
        }

        if ($stockMovement->lastStatus()["name"] != StockMovementStatus::LOADING->value) {
            throw new \InvalidArgumentException("To update status must be loading.");
        }

        $stockMovementDetail = $this->stockMovementDetailRepository->create($stockMovementDetailCreateData->except("status")->toArray());
        return StockMovementDetailResource::from($stockMovementDetail);
    }

    public function update(StockMovementDetailUpdateData $stockMovementDetailUpdateData)
    {
        if (!$stockMovementDetail = $this->stockMovementDetailRepository->findById($stockMovementDetailUpdateData->id, ["stockMovement"])) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Stock movement detail ($stockMovementDetailUpdateData->id) not found.");
        }

        if ($stockMovementDetail->stockMovement->lastStatus()["name"] != StockMovementStatus::LOADING->value) {
            throw new \InvalidArgumentException("To update status must be loading.", 422);
        }

        return $this->stockMovementDetailRepository->updateQtyUsingModel($stockMovementDetail, $stockMovementDetailUpdateData->qty);
    }

    public function delete(string $stockMovementDetailId)
    {
        return $this->stockMovementDetailRepository->delete($stockMovementDetailId);
    }
}
