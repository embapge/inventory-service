<?php

namespace App\Repositories;

use App\Models\PurchaseOrderDetail;

class PurchaseOrderDetailRepository
{
    public function update(PurchaseOrderDetail $detail, array $data)
    {
        return $detail->update([
            "qty" => $data["qty"],
            "total" => $data['qty'] * $data['product']['price'],
        ]);
    }

    public function insert(array $data)
    {
        return PurchaseOrderDetail::insert($data);
    }
}
