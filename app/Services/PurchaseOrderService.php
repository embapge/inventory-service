<?php

namespace App\Services;

use App\Dtos\ProductData;
use App\Dtos\PurchaseOrderData;
use App\Dtos\PurchaseOrderDetailData;
use App\Dtos\PurchaseOrderDetailProductData;
use App\Enums\PurchaseOrderStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Supplier;
use App\Repositories\ProductRepository;
use App\Repositories\PurchaseOrderDetailRepository;
use App\Repositories\PurchaseOrderRepository;
use App\Repositories\SupplierRepository;
use App\Resources\PurchaseOrderResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\LaravelData\PaginatedDataCollection;

class PurchaseOrderService
{
    protected PurchaseOrder $purchaseOrder;

    public function __construct(protected PurchaseOrderRepository $purchaseOrderRepository) {}

    public function setPurchaseOrder(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder;
        return $this;
    }

    public function getPurchaseOrder()
    {
        return $this->purchaseOrder;
    }

    public function calculate()
    {
        $this->purchaseOrder->load(["details", "supplier"]);

        foreach ($this->purchaseOrder->details as $detail) {
            $detail->update([
                "total" => $detail->product['price'] * $detail->qty
            ]);
        }

        $this->purchaseOrder->refresh();

        $this->purchaseOrder->update([
            "total_amount" => $this->purchaseOrder->details->sum("total")
        ]);

        $this->purchaseOrder->refresh();

        return $this;
    }

    public function getPaginated(int $limit = 15)
    {
        return PurchaseOrderResource::collect($this->purchaseOrderRepository->paginated($limit), PaginatedDataCollection::class);
    }

    public function show(string $purchaseOrderId, array $relationships = [])
    {
        if (!$purchaseOrder = $this->purchaseOrderRepository->findById($purchaseOrderId, $relationships)) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Purchase order with ($purchaseOrderId) not found.");
        }

        return PurchaseOrderResource::from($purchaseOrder);
    }

    public function store(PurchaseOrderData $purchaseOrderData, Collection $purchaseOrderDetailDatas)
    {
        if (!Supplier::find($purchaseOrderData->supplier_id)) {
            throw new \Illuminate\Support\ItemNotFoundException("Supplier dengan id: $purchaseOrderData->supplier_id tidak ditemukan", 404);
        }

        if ($purchaseOrderDetailDatas->filter(fn($detail) => !($detail instanceof PurchaseOrderDetailData))->isNotEmpty()) {
            throw new \InvalidArgumentException("Data bukan instance dari Purchase Order Detail Data", 422);
        }

        $purchaseOrder = DB::transaction(function () use ($purchaseOrderData, $purchaseOrderDetailDatas) {
            if ($purchaseOrderData->id) {
                if (!$purchaseOrder = $this->purchaseOrderRepository->findById($purchaseOrderData->id)) {
                    throw new \Illuminate\Support\ItemNotFoundException("Purchase order dengan id: $purchaseOrderData->id tidak ditemukan", 404);
                }
            } else {
                $purchaseOrder = $this->purchaseOrderRepository->create($purchaseOrderData->only("supplier_id", "order_date", "total_amount")->toArray());
            }

            $detailProductIds = $purchaseOrderDetailDatas->whereNotNull("product.id");

            if ($detailProductIds->isNotEmpty()) {
                $products = (new ProductRepository)->getById($detailProductIds->pluck("product.id")->toArray(), ["category"]);

                if ($products->isEmpty()) {
                    throw new \Illuminate\Support\ItemNotFoundException("Produk yang dicari kosong", 404);
                }

                $unexistProducts = $detailProductIds->whereNotIn("product.id", $products->pluck("id"));

                if ($unexistProducts->isNotEmpty()) {
                    throw new \Illuminate\Support\ItemNotFoundException("Product dengan id: " . $unexistProducts->pluck("product.id")->join(", ") . " tidak ditemukan", 404);
                }

                foreach ($detailProductIds as $detailData) {
                    $product = $products->where("id", $detailData->product->id)->first();
                    $detailData->product = PurchaseOrderDetailProductData::from([
                        "id" => $product->id,
                        "category_id" => $product->category_id,
                        "name" => $product->name,
                        "price" => $product->price,
                        "description" => $product->description,
                        "category" => $product->category->only("id", "name", "description")
                    ]);
                }
            }

            $detailWithoutProductIds = $purchaseOrderDetailDatas->whereNull("product.id");
            if ($detailWithoutProductIds->isNotEmpty()) {
                $categoryId = $detailWithoutProductIds->pluck("product.category_id");
                $categories = Category::whereIn("id", $categoryId)->get();

                if ($categories->isEmpty()) {
                    throw new \Illuminate\Support\ItemNotFoundException("Kategori yang dicari kosong", 404);
                }

                $unexistCategories = $detailWithoutProductIds->whereNotIn("product.category_id", $categories->pluck("id"));

                if ($unexistCategories->isNotEmpty()) {
                    throw new \Illuminate\Support\ItemNotFoundException("Kategori dengan id: " . $unexistCategories->pluck("product.category_id")->join(",") . " tidak ditemukan", 404);
                }

                foreach ($detailWithoutProductIds as $detailData) {
                    $productModel = (new ProductRepository)->create(["name" => $detailData->product->name, "price" => $detailData->product->price, "description" => $detailData->product->description, "category_id" => $detailData->product->category_id]);

                    $category = $categories->first(fn($category) => $category->id == $detailData->product->category_id);
                    $detailData->product = PurchaseOrderDetailProductData::from([
                        "id" => $productModel->id,
                        "category_id" => $category->id,
                        "name" => $productModel->name,
                        "price" => $productModel->price,
                        "description" => $productModel->description,
                        "category" => $category->only("id", "name", "description")
                    ]);
                }
            }

            (new PurchaseOrderDetailRepository)->insert($purchaseOrderDetailDatas->map(fn($detail) => ["id" => Str::uuid(), "purchase_order_id" => $purchaseOrder->id, "product" => json_encode($detail->product), "qty" => $detail->qty])->toArray());

            return $purchaseOrder->fresh();
        });

        return PurchaseOrderResource::from($purchaseOrder->load(["details", "supplier"]));
    }

    public function update(PurchaseOrderData $purchaseOrderData, PurchaseOrder $purchaseOrder)
    {
        if (!(new SupplierRepository)->findById($purchaseOrderData->supplier_id)) {
            throw new \Illuminate\Support\ItemNotFoundException("Supplier_id id: $purchaseOrderData->supplier_id tidak ditemukan");
        }

        if ($purchaseOrder->status != PurchaseOrderStatus::PENDING && $purchaseOrder->status != PurchaseOrderStatus::PENDING) {
            throw new \InvalidArgumentException("Purchase order status must be pending or loading", 422);
        }

        return $this->purchaseOrderRepository->update($purchaseOrder, [
            "supplier_id" => $purchaseOrderData->supplier_id,
            "order_date" => $purchaseOrderData->order_date,
            "status" => $purchaseOrderData->status,
        ]);
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        return $this->purchaseOrderRepository->delete($purchaseOrder);
    }
}
