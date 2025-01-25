<?php

namespace App\Repositories;

use App\Models\PurchaseOrder;
use App\Resources\PurchaseOrderResource;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;

class PurchaseOrderRepository
{
    public function paginated(int $limit = 15)
    {
        return PurchaseOrder::with(["supplier", "details"])->paginate($limit > 20 ? 20 : $limit);
    }

    public function findById(string $id, array $relationships = [])
    {
        $purchaseOrder =  PurchaseOrder::with("supplier")->where("id", $id);
        $purchaseOrder = validateEagerLoadRelation($purchaseOrder, new PurchaseOrder(), $relationships);
        return $purchaseOrder->first();
    }

    public function findByIds(array $id, array $relationship = []): Collection
    {
        return PurchaseOrder::with($relationship)->whereIn("id", $id)->get();
    }

    public function create(array $data)
    {
        return PurchaseOrder::create($data);
    }

    public function update(PurchaseOrder $purchaseOrder, array $data)
    {
        return $purchaseOrder->update($data);
    }

    public function delete(PurchaseOrder $purchaseOrder)
    {
        return $purchaseOrder->delete();
    }
}
