<?php

namespace App\Observers;

use App\Models\Shelf;
use Illuminate\Support\Str;

class ShelfObserver
{
    public function creating(Shelf $shelf): void
    {
        do {
            $generateCode = Str::lower(Str::random(10));
        } while (Shelf::firstWhere(["code" => $generateCode]));

        $shelf->code = $generateCode;
    }

    public function created(Shelf $shelf): void
    {
        //
    }

    public function updated(Shelf $shelf): void
    {
        //
    }

    /**
     * Handle the Shelf "deleted" event.
     */
    public function deleted(Shelf $shelf): void
    {
        //
    }

    /**
     * Handle the Shelf "restored" event.
     */
    public function restored(Shelf $shelf): void
    {
        //
    }

    /**
     * Handle the Shelf "force deleted" event.
     */
    public function forceDeleted(Shelf $shelf): void
    {
        //
    }
}
