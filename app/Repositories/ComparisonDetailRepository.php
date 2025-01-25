<?php

namespace App\Repositories;

use App\Models\ComparisonDetail;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class ComparisonDetailRepository
{
    public function findById(string $id)
    {
        return ComparisonDetail::find($id);
    }

    public function insert(array $data)
    {
        return ComparisonDetail::insert($data);
    }

    public function delete(string $id)
    {
        try {
            if (!$comparisonDetail = ComparisonDetail::find($id)) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Comparison with id $id not found.");
            }

            return $comparisonDetail->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                throw new \App\Exceptions\ForeignKeyConstraintException("Cannot delete comparison detail ($id) record because it is referenced in other records.");
            }

            Log::error('QueryException encountered', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'code' => $e->getCode(),
            ]);

            throw new \Exception("Failed to delete comparison detail", 500);
        }
    }
}
