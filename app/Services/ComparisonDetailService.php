<?php

namespace App\Services;

use App\Dtos\ComparisonDetailData;
use App\Models\Comparison;
use App\Models\ComparisonDetail;
use App\Models\PurchaseOrder;
use App\Repositories\ComparisonDetailRepository;
use Spatie\LaravelData\Data;

class ComparisonDetailService
{
    public function __construct(protected ComparisonDetailRepository $comparisonDetailRepository) {}

    public function destroy(ComparisonDetail $detail)
    {
        return $this->comparisonDetailRepository->delete($detail);
    }
}
