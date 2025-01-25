<?php

namespace App\Observers;

use App\Models\Comparison;
use App\Services\ComparisonService;

class ComparisonObserver
{
    public function __construct(protected ComparisonService $service) {}

    public function creating(Comparison $comparison)
    {
        $comparison->number_display = $this->service->getNumber() . "/PRCHS/CMPR/" . now()->format("Y");
    }
    /**
     * Handle the Comparison "created" event.
     */
    public function created(Comparison $comparison): void
    {
        //
    }

    /**
     * Handle the Comparison "updated" event.
     */
    public function updated(Comparison $comparison): void
    {
        //
    }

    /**
     * Handle the Comparison "deleted" event.
     */
    public function deleted(Comparison $comparison): void
    {
        //
    }

    /**
     * Handle the Comparison "restored" event.
     */
    public function restored(Comparison $comparison): void
    {
        //
    }

    /**
     * Handle the Comparison "force deleted" event.
     */
    public function forceDeleted(Comparison $comparison): void
    {
        //
    }
}
