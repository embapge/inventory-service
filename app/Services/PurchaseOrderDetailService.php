<?php

namespace App\Services;

use App\Dtos\ProductData;
use App\Dtos\PurchaseOrderDetailData;
use App\Dtos\PurchaseOrderDetailProductData;
use App\Models\Category;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Repositories\PurchaseOrderDetailRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PurchaseOrderDetailService
{
    public function __construct(protected PurchaseOrderService $purchaseOrderService, protected PurchaseOrderDetailRepository $purchaseOrderDetailRepository) {}

    public function update(PurchaseOrderDetailData $detailData, PurchaseOrderDetail $purchaseOrderDetail)
    {
        $this->purchaseOrderDetailRepository->update($purchaseOrderDetail, $detailData->only("qty", "product")->toArray());
        $this->purchaseOrderService->setPurchaseOrder($purchaseOrderDetail->purchaseOrder)->calculate();

        return true;
    }

    public function destroy(PurchaseOrderDetail $detail)
    {
        return $detail->delete();
    }
}
