<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\StockMovementAddRemoveShelfData;
use App\Dtos\StockMovementUpdateData;
use App\Dtos\StockMovementCreateData;
use App\Dtos\StockMovementProgressData;
use App\Http\Controllers\Controller;
use App\Models\StockMovement;
use App\Services\StockMovementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Fluent;

class StockMovementController extends Controller
{
    public function __construct(public StockMovementService $service) {}

    public function index(Request $request)
    {
        return $this->service->getPaginated($request->input("limit", 15));
    }

    public function show(string $stockMovementId)
    {
        return $this->service->show($stockMovementId);
    }

    public function store(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                "source.purchase_order_id" => "required_without:source.warehouse_id|uuid",
                "source.warehouse_id" => "required_without:source.purchase_order_id|uuid",
                "warehouse_id" => "required|uuid",
                "movement_date" => "required|date_format:Y-m-d\\TH:i:sP",
                "reason" => "required|string",
                "type" => "required|string"
            ]
        );

        $validator->sometimes(["products"], "array|required", function (Fluent $input) use ($request) {
            return $request->has("source.warehouse_id");
        });

        $validator->sometimes(["products.*.id"], "uuid|required", function (Fluent $input) use ($request) {
            return $request->has("source.warehouse_id");
        });

        $validator->sometimes(["products.*.qty"], "numeric|required|gt:0", function (Fluent $input) use ($request) {
            return $request->has("source.warehouse_id");
        });

        if ($validator->fails()) {
            return response()->json(["message" => "Validation Error", "errors" => $validator->errors()], 422);
        }

        $products = null;
        if ($request->has("source.warehouse_id")) {
            $type = "warehouse";
            $products = $request->products;
        } else {
            $type = "purchaseOrder";
        }

        $stockMovementData = StockMovementCreateData::from(array_merge(["stockable_id" => $request->input("source.warehouse_id", $request->input("source.purchase_order_id")), "stockable_type" => $type,  "movement_date" => $request->movement_date, "products" => $products], $request->only("warehouse_id", "type", "reason")));

        $stockMovementResource = $this->service->create($stockMovementData);
        return response()->json(["data" => $stockMovementResource, "message" => "Data has been created"]);
    }

    public function update(StockMovementUpdateData $stockMovementupdateData)
    {
        $this->service->update($stockMovementupdateData);
        return response()->json(["message" => "Data has been changed."]);
    }

    public function status(StockMovementProgressData $stockMovementProgressData)
    {
        $this->service->updateStatus($stockMovementProgressData);
        return response()->json(["message" => "Data has been changed."]);
    }

    public function insertProductToShelf(Request $request, string $stockMovementId)
    {
        $request->validate([
            'insert' => 'required|array|min:1',
            "insert.*.shelf_id" => "uuid|required",
            "insert.*.product.id" => "uuid|required",
            "insert.*.product.qty" => "integer|gt:0",
        ]);

        $this->service->insertProduct($stockMovementId, StockMovementAddRemoveShelfData::collect(collect($request->insert)->map(fn($addToShelf) => ["product_id" => $addToShelf["product"]["id"], "shelf_id" => $addToShelf["shelf_id"], "qty" => $addToShelf["product"]["qty"]])));
        return response()->json(["message" => "Product successful added to shelf."]);
    }

    public function removeProductFromShelf(Request $request, string $stockMovementId)
    {
        $request->validate([
            'remove' => 'required|array|min:1',
            "remove.*.shelf_id" => "uuid|required",
            "remove.*.product.id" => "uuid|required",
            "remove.*.product.qty" => "integer|gt:0",
        ]);

        $this->service->removeProduct($stockMovementId, StockMovementAddRemoveShelfData::collect(collect($request->remove)->map(fn($removeFromShelf) => ["product_id" => $removeFromShelf["product"]["id"], "shelf_id" => $removeFromShelf["shelf_id"], "qty" => $removeFromShelf["product"]["qty"]])));
        return response()->json(["message" => "Product successful removed from shelf."]);
    }

    public function destroy(string $stockMovementId)
    {
        $this->service->destroy($stockMovementId);
        return response()->json(["message" => "Data berhasil deleted."]);
    }
}
