<?php

namespace App\Http\Controllers;

use App\Dtos\StockMovementDetailCreateData;
use App\Dtos\StockMovementDetailUpdateData;
use App\Repositories\StockMovementDetailRepository;
use App\Services\StockMovementDetailService;

class StockMovementDetailController extends Controller
{
    public function __construct(protected StockMovementDetailService $service)
    {
        //
    }

    public function store(StockMovementDetailCreateData $stockMovementDetailCreateData)
    {
        return $this->service->create($stockMovementDetailCreateData->except("status"));
    }

    public function update(StockMovementDetailUpdateData $stockMovementDetailUpdateData)
    {
        return $this->service->update($stockMovementDetailUpdateData);
        return response()->json(["message" => "Data has been updated."]);
    }

    public function destroy(string $stockMovementId)
    {
        $this->service->delete($stockMovementId);
        return response()->json(["message" => "Data has been deleted."]);
    }
}
