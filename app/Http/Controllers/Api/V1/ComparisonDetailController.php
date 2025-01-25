<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\CategoryData;
use App\Dtos\ComparisonData;
use App\Dtos\ComparisonDetailData;
use App\Dtos\ProductData;
use App\Dtos\PurchaseOrderData;
use App\Dtos\PurchaseOrderDetailData;
use App\Enums\ComparisonDetailStatus;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Comparison;
use App\Models\ComparisonDetail;
use App\Models\Product;
use App\Resources\ComparisonResource;
use App\Services\ComparisonDetailService;
use App\Services\ComparisonService;
use App\Services\ProductService;
use App\Services\PurchaseOrderDetailService;
use App\Services\PurchaseOrderService;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ComparisonDetailController extends Controller
{
    public function __construct(public ComparisonDetailService $service) {}

    public function destroy(ComparisonDetail $comparisonDetail)
    {
        $this->service->destroy($comparisonDetail);
        return response()->json(["message" => "Data has been deleted."]);
    }
}
