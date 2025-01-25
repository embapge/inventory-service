<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Dtos\WarehouseData;
use App\Models\Warehouse;
use App\Resources\WarehouseResource;
use App\Services\WarehouseService;
use Illuminate\Http\Request;
use Spatie\LaravelData\PaginatedDataCollection;

class WarehouseController extends Controller
{
    public function __construct(protected WarehouseService $service)
    {
        //
    }

    public function index(Request $request)
    {
        return $this->service->getPaginated($request->input("limit", 15));
    }

    public function store(WarehouseData $warehouseData)
    {
        $warehouse = $this->service->create($warehouseData->except("id"));
        return response()->json(["data" => WarehouseResource::from($warehouse), "message" => "Data has been created"], 201);
    }

    public function show(string $warehouseId)
    {
        $warehouseResource = $this->service->show($warehouseId);
        return response()->json(["data" => $warehouseResource]);
    }

    public function showShelfs(Request $request, string $warehouseId)
    {
        return $this->service->showShelfs($warehouseId, $request->input("category"));
    }

    public function update(WarehouseData $warehouseData)
    {
        $this->service->update($warehouseData);
        return response()->json(["message" => "Data has been changed."]);
    }

    public function destroy(string $warehouseId)
    {
        $this->service->delete($warehouseId);
        return response()->json(["message" => "Data has been deleted."]);
    }
}
