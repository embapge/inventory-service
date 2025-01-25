<?php

use App\Exceptions\InvalidModelRelationException;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

function ensureRelationshipExists($model, $relationship)
{
    if (!method_exists($model, $relationship)) {
        throw new ModelNotFoundException("Relationship '{$relationship}' not found in " . get_class($model));
    }

    if (!$model->$relationship() instanceof \Illuminate\Database\Eloquent\Relations\Relation) {
        throw new InvalidModelRelationException("The method '{$relationship}' is not a valid Eloquent relationship.");
    }
}

function validateDateRequest(string $date)
{
    if (CarbonImmutable::parse($date)->format("Y-m-d") > now()->format("Y-m-d")) {
        throw new \InvalidArgumentException("Date must be larger than " . now()->format("Y-m-d"), 422);
    }
}
