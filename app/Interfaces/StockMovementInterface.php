<?php

namespace App\Interfaces;

use App\Dtos\StockMovement\CreateData as StockMovementCreateData;

interface StockMovementInterface
{
    public function store(StockMovementCreateData $stockMovementCreateData);
}
