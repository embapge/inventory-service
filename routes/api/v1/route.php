<?php

use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ComparisonController;
use App\Http\Controllers\Api\V1\ComparisonDetailController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\WarehouseController;
use App\Http\Controllers\Api\V1\ShelfController;
use App\Http\Controllers\Api\V1\SupplierController;
use App\Http\Controllers\Api\V1\PurchaseOrderDetailController;
use App\Http\Controllers\Api\V1\PurchaseOrderController;
use App\Http\Controllers\Api\V1\StockMovementController;
use App\Http\Controllers\Api\V1\StockMovementDetailController;
use Illuminate\Support\Facades\Route;

Route::group(["controller" => ProductController::class, "prefix" => "product"], function () {
    Route::name("api.v1.product.")->group(function () {
        Route::get("", "index")->name("index");
        Route::get("{productId}", "show")->name("show");
        Route::post("", "store")->name("store");
        Route::patch("{productId}", "update")->name("update");
        Route::post("{productId}", "destroy")->name("destroy");
    });
});

Route::group(["controller" => CategoryController::class, "prefix" => "category"], function () {
    Route::name("api.v1.category.")->group(function () {
        Route::get("", "index")->name("index");
        Route::get("{categoryId}", "show")->name("show");
        Route::post("", "store")->name("store");
        Route::patch("{categoryId}", "update")->name("update");
        Route::post("{categoryId}", "destroy")->name("destroy");
    });
});

Route::group(["controller" => WarehouseController::class, "prefix" => "warehouse"], function () {
    Route::name("api.v1.warehouse.")->group(function () {
        Route::get("", "index")->name("index");
        Route::get("{warehouse}", "show")->name("show");
        Route::get("{warehouse}/shelfs", "showShelfs")->name("show.shelfs");
        Route::post("", "store")->name("store");
        Route::patch("{warehouseId}", "update")->name("update");
        Route::post("{warehouseId}", "destroy")->name("destroy");
    });
});

Route::group(["controller" => SupplierController::class, "prefix" => "supplier"], function () {
    Route::name("api.v1.supplier.")->group(function () {
        Route::get("", "index")->name("index");
        Route::get("{supplier}", "show")->name("show");
        Route::post("", "store")->name("store");
        Route::patch("{supplierId}", "update")->name("update");
        Route::post("{supplierId}", "destroy")->name("destroy");
    });
});

Route::group(["controller" => ShelfController::class, "prefix" => "shelf"], function () {
    Route::name("api.v1.shelf.")->group(function () {
        Route::get("", "index")->name("index");
        Route::get("{shelfId}", "show")->name("show");
        Route::post("", "store")->name("store");
        Route::patch("{shelfId}", "update")->name("update");
        Route::post("{shelfId}", "destroy")->name("destroy");
    });
});

Route::group(["controller" => PurchaseOrderController::class, "prefix" => "purchase-order"], function () {
    Route::name("api.v1.purchase_order.")->group(function () {
        Route::get("", "index")->name("index");
        Route::get("{purchaseOrder}", "show")->name("show");
        Route::post("", "store")->name("store");
        Route::patch("{purchaseOrder}", "update")->name("update");
        Route::post("{purchaseOrder}", "destroy")->name("destroy");
    });
});

Route::group(["controller" => PurchaseOrderDetailController::class, "prefix" => "purchase-order-detail"], function () {
    Route::name("api.v1.purchase_order_detail.")->group(function () {
        Route::patch("{purchaseOrderDetail}", "update")->name("update");
        Route::post("{purchaseOrderDetail}", "destroy")->name("destroy");
    });
});

Route::group(["controller" => ComparisonController::class, "prefix" => "comparison"], function () {
    Route::name("api.v1.comparison.")->group(function () {
        Route::get("", "index")->name("index");
        Route::get("{comparison}", "show")->name("show");
        Route::post("", "store")->name("store");
        Route::patch("{comparisonId}", "update")->name("update");
        Route::post("{comparisonId}", "destroy")->name("destroy");
    });
});

Route::group(["controller" => ComparisonDetailController::class, "prefix" => "comparison-detail"], function () {
    Route::name("api.v1.comparison_detail.")->group(function () {
        Route::post("{comparisonDetailId}", "destroy")->name("destroy");
    });
});

Route::group(["controller" => StockMovementController::class, "prefix" => "stock-movement"], function () {
    Route::name("api.v1.stock_movement.")->group(function () {
        Route::get("", "index")->name("index");
        Route::get("{stockMovementId}", "show")->name("show");
        Route::post("", "store")->name("store");
        Route::patch("{stockMovementId}", "update")->name("update");
        Route::post("{stockMovement}", "destroy")->name("destroy");
        Route::patch("{stockMovementId}/status", "status")->name("status");
        Route::post("{stockMovementId}/add-to-shelf", "insertProductToShelf")->name("add_to_shelf");
        Route::post("{stockMovementId}/remove-from-shelf", "removeProductFromShelf")->name("remove_from_shelf");
    });
});

Route::group(["controller" => StockMovementDetailController::class, "prefix" => "stock-movement-detail"], function () {
    Route::name("api.v1.stock_movement_detail.")->group(function () {
        Route::post("/{stockMovement}/stockMovement", "store")->name("store");
        Route::patch("{stockMovementDetailId}", "update")->name("update");
        Route::post("{stockMovementDetail}", "destroy")->name("destroy");
    });
});
