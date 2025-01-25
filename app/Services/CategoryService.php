<?php

namespace App\Services;

use App\Dtos\CategoryData;
use App\Models\Category;
use App\Repositories\CategoryRepository;
use App\Resources\CategoryResource;
use Spatie\LaravelData\PaginatedDataCollection;

class CategoryService
{
    public Category $category;

    public function __construct(protected CategoryRepository $categoryRepository) {}

    public function getPaginated(int $limit = 15)
    {
        return CategoryResource::collect($this->categoryRepository->paginated($limit), PaginatedDataCollection::class);
    }

    public function create(CategoryData $categoryData)
    {
        $category = $this->categoryRepository->create($categoryData->except("id")->toArray());
        return CategoryResource::from($category);
    }

    public function update(CategoryData $categoryData)
    {
        return $this->categoryRepository->update($categoryData->id, $categoryData->except("id")->toArray());
    }

    public function delete(string $categoryId)
    {
        return $this->categoryRepository->delete($categoryId);
    }

    public function show(string $categoryId, array $relationships = [])
    {
        $category = $this->categoryRepository->show($categoryId, $relationships);

        if (!$category) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Category with id (" . $categoryId . ") not found.");
        }

        return CategoryResource::from($category);
    }

    public function destroy(Category $category)
    {
        return $category->delete();
    }
}
