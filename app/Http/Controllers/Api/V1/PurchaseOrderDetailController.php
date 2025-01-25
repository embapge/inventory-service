<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\CategoryData;
use App\Dtos\ProductData;
use App\Dtos\PurchaseOrderDetailData;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Resources\PurchaseOrderDetailResource;
use App\Services\ProductService;
use App\Services\PurchaseOrderDetailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderDetailController extends Controller
{
    public function __construct(public PurchaseOrderDetailService $service) {}

    public function update(Request $request, PurchaseOrderDetail $purchaseOrderDetail)
    {
        $request->validate([
            "qty" => "numeric|gt:0"
        ]);

        $this->service->update(PurchaseOrderDetailData::from(array_merge($purchaseOrderDetail->toArray(), ["qty" => $request->input("qty", $purchaseOrderDetail->qty)])), $purchaseOrderDetail);
        return response()->json(["message" => "Data has been changed"]);
    }

    public function destroy(PurchaseOrderDetail $purchaseOrderDetail)
    {
        $this->service->destroy($purchaseOrderDetail);
        return response()->json(["message" => "Data has been deleted."]);
    }
}
