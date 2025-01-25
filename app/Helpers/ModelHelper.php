<?php

function validateEagerLoadRelation($query, $model, array $relationships = [])
{
    if (collect($relationships)->isNotEmpty()) {
        foreach ($relationships as $relationship) {
            ensureRelationshipExists($model, $relationship);
        }
        $query = $query->with($relationships);
    }

    return $query;
}
