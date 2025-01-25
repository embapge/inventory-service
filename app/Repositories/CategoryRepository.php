<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CategoryRepository
{
    public function paginated(int $limit)
    {
        return Category::paginate($limit > 50 ? 50 : $limit);
    }

    public function show(string $id, array $relationships = [])
    {
        $category =  Category::where("id", $id);
        $category = validateEagerLoadRelation($category, new Category(), $relationships);
        return $category->first();
    }

    public function findById(string $id, array $relationships = [])
    {
        return Category::with($relationships)->where("id", $id)->first();
    }

    public function getById(array $ids, array $relationships = [])
    {
        return Category::with($relationships)->whereIn("id", $ids)->get();
    }

    public function create(array $data)
    {
        return Category::create($data)->fresh();
    }

    public function update(string $id, array $data)
    {
        if (!$category = Category::find($id)) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Category with id $id not found.");
        }

        return $category->update($data);
    }

    public function delete(string $id)
    {
        try {
            if (!$category = Category::find($id)) {
                throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Category with id $id not found.");
            }

            return $category->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                throw new \App\Exceptions\ForeignKeyConstraintException("Cannot delete category ($id) record because it is referenced in other records.");
            }

            Log::error('QueryException encountered', [
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
                'code' => $e->getCode(),
            ]);

            throw new \Exception("Failed to delete category", 500);
        }
    }
}
