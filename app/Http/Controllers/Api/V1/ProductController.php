<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\ProductData;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Spatie\LaravelData\PaginatedDataCollection;

class ProductController extends Controller
{
    public function __construct(protected ProductService $service)
    {
        //
    }

    public function index(Request $request)
    {
        return $this->service->getPaginated($request->input("limit", 15));
    }

    public function store(ProductData $productData)
    {
        $productResource = $this->service->create($productData);
        return response()->json(["data" => $productResource, "message" => "Product has been created."], 201);
    }

    public function show(Request $request, string $productId)
    {
        $productResource = $this->service->show($productId, $request->input("relationship") ? explode(",", $request->relationship) : []);
        return response()->json(["data" => $productResource]);
    }

    public function update(ProductData $productData)
    {
        $this->service->update($productData);
        return response()->json(["message" => "Data has been changed."]);
    }

    public function destroy(string $productId)
    {
        $this->service->delete($productId);
        return response()->json(["message" => "Data successfully deleted."]);
    }
}
