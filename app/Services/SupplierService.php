<?php

namespace App\Services;

use App\Dtos\SupplierData;
use App\Models\Supplier;
use App\Repositories\SupplierRepository;
use App\Resources\SupplierResource;
use Spatie\LaravelData\PaginatedDataCollection;

class SupplierService
{
    public function __construct(protected SupplierRepository $supplierRepository) {}

    public function getPaginated(int $limit = 15)
    {
        return SupplierResource::collect($this->supplierRepository->paginatedWithRelationships($limit), PaginatedDataCollection::class);
    }

    public function create(SupplierData $supplierData)
    {
        $supplier = $this->supplierRepository->create($supplierData->except("id")->toArray());
        return SupplierResource::from($supplier);
    }

    public function update(SupplierData $supplierData)
    {
        return $this->supplierRepository->update($supplierData->id, $supplierData->except("id")->toArray());
    }

    public function delete(string $supplierId)
    {
        return $this->supplierRepository->delete($supplierId);
    }

    public function show(string $supplierId)
    {
        $supplier = $this->supplierRepository->show($supplierId);

        if (!$supplier) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Supplier with id (" . $supplierId . ") not found.");
        }

        return SupplierResource::from($supplier);
    }

    public function destroy(string $supplierId)
    {
        return $this->supplierRepository->delete($supplierId);
    }
}
