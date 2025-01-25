<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Dtos\SupplierData;
use App\Resources\SupplierResource;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct(protected SupplierService $service)
    {
        //
    }

    public function index(Request $request)
    {
        return $this->service->getPaginated($request->input("limit", 15));
    }

    public function store(SupplierData $supplierData)
    {
        $supplier = $this->service->create($supplierData->except("id"));
        return response()->json(["data" => SupplierResource::from($supplier), "message" => "Data has been created"], 201);
    }

    public function show(string $supplierId)
    {
        $supplierResource = $this->service->show($supplierId);
        return response()->json(["data" => $supplierResource]);
    }

    public function update(SupplierData $supplierData)
    {
        $this->service->update($supplierData);
        return response()->json(["message" => "Data has been changed."]);
    }

    public function destroy(string $supplierId)
    {
        $this->service->delete($supplierId);
        return response()->json(["message" => "Data has been deleted."]);
    }
}
