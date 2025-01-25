<?php

namespace App\Services;

use App\Dtos\ProductData;
use App\Models\Product;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Resources\ProductResource;
use Spatie\LaravelData\PaginatedDataCollection;

class ProductService
{
    public Product $product;

    public function __construct(protected ProductRepository $productRepository, protected CategoryRepository $categoryRepository) {}

    public function getPaginated(int $limit = 15)
    {
        return ProductResource::collect($this->productRepository->paginated($limit), PaginatedDataCollection::class);
    }

    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    public function create(ProductData $productData)
    {
        if (!$category = $this->categoryRepository->findById($productData->category_id)) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Category ($productData->category_id) not found.");
        }
        
        $product = $this->productRepository->create($productData->only("category_id", "name", "price", "description", "reorder_level")->toArray());
        return ProductResource::from($product);
    }

    public function update(ProductData $productData)
    {
        if (!$category = $this->categoryRepository->findById($productData->category_id)) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Category ($productData->category_id) not found.");
        }

        return $this->productRepository->update($productData->id, $productData->except("id", "category")->toArray());
    }

    public function delete(string $productId)
    {
        return $this->productRepository->delete($productId);
    }

    public function stockQty()
    {
        return (int) $this->product->stocks->sum("pivot.qty");
    }

    public function show(string $productId, array $relationships = [])
    {
        $product = $this->productRepository->findById($productId, $relationships);

        if (!$product) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException("Product with id (" . $productId . ") not found.");
        }

        return ProductResource::from($product);
    }

    public function destroy(string $productId)
    {
        return $this->productRepository->delete($productId);
    }
}
