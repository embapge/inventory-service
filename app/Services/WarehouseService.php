<?php

namespace App\Services;

use App\Dtos\WarehouseData;
use App\Models\Warehouse;
use App\Repositories\WarehouseRepository;
use App\Resources\WarehouseResource;
use Spatie\LaravelData\PaginatedDataCollection;

class WarehouseService
{
    public function __construct(protected WarehouseRepository $warehouseRepository) {}

    public function getPaginated(int $limit = 15)
    {
        return WarehouseResource::collect($this->warehouseRepository->paginated($limit), PaginatedDataCollection::class);
    }

    public function create(WarehouseData $warehouseData)
    {
        $warehouse = $this->warehouseRepository->create($warehouseData->except("id")->toArray());
        return WarehouseResource::from($warehouse);
    }

    public function update(WarehouseData $warehouseData)
    {
        return $this->warehouseRepository->update($warehouseData->id, $warehouseData->except("id")->toArray());
    }

    public function delete(string $warehouseId)
    {
        return $this->warehouseRepository->delete($warehouseId);
    }

    public function show(string $warehouseId)
    {
        $warehouse = $this->warehouseRepository->findById($warehouseId);

        if (!$warehouse) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Warehouse with id (" . $warehouseId . ") not found.");
        }

        return WarehouseResource::from($warehouse);
    }

    public function showShelfs(string $warehouseId, ?string $category_id)
    {
        if ($category_id) {
            $warehouse = $this->warehouseRepository->findByIdAndCategories($warehouseId, explode(",", $category_id));
        } else {
            $warehouse = $this->warehouseRepository->findById($warehouseId);
        }

        if (!$warehouse) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Warehouse with id (" . $warehouseId . ") not found.");
        }

        return WarehouseResource::from($warehouse);
    }

    public function destroy(string $warehouseId)
    {
        return $this->warehouseRepository->delete($warehouseId);
    }
}
