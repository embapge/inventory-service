<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Dtos\PurchaseOrderData;
use App\Dtos\PurchaseOrderDetailData;
use App\Models\PurchaseOrder;
use App\Resources\PurchaseOrderResource;
use App\Services\PurchaseOrderService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function __construct(public PurchaseOrderService $service) {}

    public function index(Request $request)
    {
        return $this->service->getPaginated($request->input("limit", 15));
    }

    public function store(Request $request)
    {
        $request->validate([
            "order_date" => "required_without:id|date_format:Y-m-d\\TH:i:sP",
            "supplier_id" => "required_without:id|string|uuid",
            "products" => "required|array",
            "products.*.id" => "nullable|uuid|required_without:products.*.name",
            "products.*.name" => "required_without:products.*.id",
            "products.*.price" => "required_without:products.*.id|numeric",
            "products.*.qty" => "required|integer|gt:0",
            "products.*.category_id" => "uuid|required_without:products.*.id"
        ]);

        $purchaseOrderResource = $this->service->store(PurchaseOrderData::from(["id" => $request->input("id"), "supplier_id" => $request->supplier_id, "order_date" => CarbonImmutable::parse($request->order_date)]), PurchaseOrderDetailData::collect(collect($request->products)->map(fn($product) => ["product" => collect($product)->only("id", "name", "price", "description", "category_id")->toArray(), "qty" => $product["qty"]])));

        return response()->json(["data" => $purchaseOrderResource, "message" => "Data has been created"], 201);
    }

    public function show(Request $request, string $purchaseOrderId)
    {
        return $this->service->show($purchaseOrderId, $request->input("relationship") ? explode(",", $request->relationship) : []);
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $this->service->update(PurchaseOrderData::from(array_merge([
            $purchaseOrder->toArray(),
            "supplier_id" => $request->input("supplier_id", $purchaseOrder->supplier_id),
            "order_date" => $request->input("order_date") ? CarbonImmutable::parse($request->order_date) : $purchaseOrder->order_date,
            "status" => $request->input("status", $purchaseOrder->status),
        ])), $purchaseOrder);

        return response()->json(["message" => "Data has been changed."]);
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $this->service->destroy($purchaseOrder);
        return response()->json(["message" => "Data berhasil deleted."]);
    }
}
