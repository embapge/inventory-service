<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\DTOs\ShelfData;
use App\Models\Shelf;
use App\Resources\ShelfResource;
use App\Services\ShelfService;
use Illuminate\Http\Request;
use Spatie\LaravelData\PaginatedDataCollection;

class ShelfController extends Controller
{
    public function __construct(public ShelfService $service) {}

    public function index(Request $request)
    {
        return $this->service->getPaginated($request->input("relationship") ? explode(",", $request->relationship) : [], $request->input("limit", 15));
    }

    public function store(ShelfData $shelfData)
    {
        $shelfResource = $this->service->create($shelfData);
        return response()->json(["data" => $shelfResource, "message" => "Data has been created"], 201);
    }

    public function show(string $shelfId)
    {
        $shelfResource = $this->service->show($shelfId);
        return response()->json(["data" => $shelfResource]);
    }

    public function update(ShelfData $shelfData)
    {
        $this->service->update($shelfData);
        return response()->json(["message" => "Data has been changed."]);
    }

    public function destroy(string $shelfId)
    {
        $this->service->destroy($shelfId);
        return response()->json(["message" => "Data has been deleted."]);
    }
}
