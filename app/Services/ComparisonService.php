<?php

namespace App\Services;

use App\Dtos\ComparisonData;
use App\Dtos\ComparisonDetailData;
use App\Dtos\PurchaseOrderData;
use App\Models\Comparison;
use App\Repositories\ComparisonDetailRepository;
use App\Repositories\ComparisonRepository;
use App\Resources\ComparisonResource;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\LaravelData\PaginatedDataCollection;

class ComparisonService
{
    public function __construct(protected ComparisonRepository $comparisonRepository, protected PurchaseOrderService $purchaseOrderService) {}

    public function getPaginated(int $limit = 15)
    {
        return ComparisonResource::collect($this->comparisonRepository->paginatedWithRelationships($limit), PaginatedDataCollection::class);
    }

    public function store(ComparisonData $comparisonData, Collection $purchaseOrderDatas)
    {
        if ($purchaseOrderDatas->filter(fn($purchaseOrder) => !($purchaseOrder instanceof PurchaseOrderData))->isNotEmpty()) {
            throw new \InvalidArgumentException('Collection must instanceof purchase order data', 422);
        }

        validateDateRequest($comparisonData->order_date);

        $comparison = DB::transaction(function () use ($comparisonData, $purchaseOrderDatas) {
            $purchaseOrderModels = collect([]);
            foreach ($purchaseOrderDatas as $purchaseOrderData) {
                $purchaseOrderModels->push($this->purchaseOrderService->store($purchaseOrderData, $purchaseOrderData->details));
            }

            if ($comparisonData->id) {
                if (!$comparison = $this->comparisonRepository->findById($comparisonData->id)) {
                    throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Comparison with id: " . $comparisonData->id . " not found.", 404);
                }

                $this->comparisonRepository->update($comparison, [
                    "order_date" => $comparisonData->order_date,
                ]);
            } else {
                $comparison = $this->comparisonRepository->create([
                    "order_date" => $comparisonData->order_date,
                ]);
            }

            (new ComparisonDetailRepository)->insert($purchaseOrderModels->map(fn($purchaseOrder) => ["id" => Str::uuid(), "purchase_order_id" => $purchaseOrder->id, "comparison_id" => $comparison->id])->toArray());

            return $comparison;
        });

        return ComparisonResource::from($this->comparisonRepository->loadAllRelation($comparison));
    }

    public function update(ComparisonData $comparisonData)
    {
        validateDateRequest($comparisonData->order_date);
        return $this->comparisonRepository->update($comparisonData->id, $comparisonData->only("order_date")->toArray());
    }

    public function destroy(string $comparisonId)
    {
        return $this->comparisonRepository->delete($comparisonId);
    }

    public function getNumber()
    {
        $number = 1;
        $numberDisplay = $this->comparisonRepository->findLastNumberDisplay();

        if ($numberDisplay) {
            $number = (int) explode("/", $numberDisplay)[0] + 1;
        }

        return str_pad($number, 5, "0", STR_PAD_LEFT);
    }
}
