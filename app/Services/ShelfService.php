<?php

namespace App\Services;

use App\DTOs\ShelfData;
use App\Models\Shelf;
use App\Repositories\ShelfRepository;
use App\Resources\ShelfResource;
use Spatie\LaravelData\PaginatedDataCollection;

class ShelfService
{
    public function __construct(protected ShelfRepository $shelfRepository, protected WarehouseService $warehouseService, protected CategoryService $categoryService) {}

    public function getPaginated(array $relationships = [], int $limit = 15)
    {
        return ShelfResource::collect($this->shelfRepository->paginatedWithRelationships($relationships, $limit), PaginatedDataCollection::class);
    }

    public function show(string $shelfId)
    {
        if (!$shelf = $this->shelfRepository->findByIdWithAllRelation($shelfId)) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Shelf ($shelfId) not found.");
        }

        return ShelfResource::from($shelf);
    }

    public function create(ShelfData $shelfData)
    {
        $this->warehouseService->show($shelfData->warehouse_id);
        $this->categoryService->show($shelfData->category_id);
        $shelf = $this->shelfRepository->create($shelfData->except("id", "code")->toArray());
        return ShelfResource::from($this->shelfRepository->lazyLoadAllRelation($shelf));
    }

    public function update(ShelfData $shelfData)
    {
        return $this->shelfRepository->update($shelfData->id, $shelfData->except("id", "code")->toArray());
    }

    public function delete(string $shelfId)
    {
        return $this->shelfRepository->delete($shelfId);
    }

    public function storeOrUpdate(ShelfData $shelfData)
    {
        return Shelf::updateOrCreate(["id" => $shelfData->id], [
            "warehouse_id" => $shelfData->warehouse_id,
            "category_id" => $shelfData->category_id,
            "code" => $shelfData->code,
            "capacity" => $shelfData->capacity,
        ]);
    }

    public function destroy(string $shelfId)
    {
        return $this->shelfRepository->delete($shelfId);
    }
}
