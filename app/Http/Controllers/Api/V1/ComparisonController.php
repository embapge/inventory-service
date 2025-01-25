<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\ComparisonData;
use App\Dtos\PurchaseOrderData;
use App\Http\Controllers\Controller;
use App\Models\Comparison;
use App\Resources\ComparisonResource;
use App\Services\ComparisonService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class ComparisonController extends Controller
{
    public function __construct(public ComparisonService $service) {}

    public function index(Request $request)
    {
        return $this->service->getPaginated($request->input("limit", 15));
    }

    public function store(Request $request)
    {
        $request->validate([
            "id" => "uuid",
            "order_date" => "required_without:id|date_format:Y-m-d\\TH:i:sP",
            "purchase_orders" => "required|array",
            "purchase_orders.*.supplier_id" => "required_without:purchase_orders.*.id|string",
            "purchase_orders.*.details.*.product.id" => "nullable|uuid|required_without:purchase_orders.*.details.*.product.name",
            "purchase_orders.*.details.*.product.name" => "required_without:purchase_orders.*.details.*.product.id",
            "purchase_orders.*.details.*.product.description" => "required_without:purchase_orders.*.details.*.product.id",
            "purchase_orders.*.details.*.product.price" => "required_without:purchase_orders.*.details.*.product.id|numeric",
            "purchase_orders.*.details.*.qty" => "required|integer|gt:0",
            "purchase_orders.*.details.*.product.category_id" => "uuid|required_without:purchase_orders.*.details.*.product.id"
        ]);

        $purchaseOrderRequest = collect($request->purchase_orders)->map(fn($purchaseOrder) => array_merge($purchaseOrder, ["order_date" => $request->order_date]));
        return $this->service->store(ComparisonData::from($request->has("id") ? $request->only("id") : ["order_date" => $request->order_date]), PurchaseOrderData::collect($purchaseOrderRequest));
    }

    public function show(Comparison $comparison)
    {
        return response()->json(ComparisonResource::from($comparison->load(["details.purchaseOrder.supplier"])));
    }

    public function update(Request $request, ComparisonData $comparisonData)
    {
        $this->service->update($comparisonData);
        return response()->json(["message" => "Data has been changed."]);
    }

    public function destroy(string $comparisonId)
    {
        $this->service->destroy($comparisonId);
        return response()->json(["message" => "Data berhasil deleted.kan"]);
    }
}
