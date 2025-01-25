<?php

namespace App\Http\Controllers\Api\V1;

use App\Dtos\CategoryData;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Resources\CategoryResource;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\LaravelData\PaginatedDataCollection;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $service)
    {
        //
    }

    public function index(Request $request)
    {
        return $this->service->getPaginated($request->input("limit", 15));
    }

    public function store(CategoryData $categoryData)
    {
        $categoryResource = $this->service->create($categoryData->except("id"));
        return response()->json(["data" => $categoryResource, "message" => "Data berhasil ditambahkan"], 201);
    }

    public function show(Request $request, string $categoryId)
    {
        $categoryResource = $this->service->show($categoryId, $request->input("relationship") ? explode(",", $request->relationship) : []);

        return response()->json(["data" => $categoryResource]);
    }

    public function update(CategoryData $categoryData)
    {
        $this->service->update($categoryData);
        return response()->json(["message" => "Data has been changed."]);
    }

    public function destroy(string $categoryId)
    {
        $this->service->delete($categoryId);
        return response()->json(["message" => "Data has been deleted."]);
    }
}
