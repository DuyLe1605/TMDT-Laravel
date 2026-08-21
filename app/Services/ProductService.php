<?php

namespace App\Services;

use App\Constants\AppConstants;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService
{
    /**
     * Get paginated list of products with filters.
     *
     * @param int|null $categoryId
     * @param string|null $search
     * @param string|null $sort
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedProducts(
        ?int $categoryId = null,
        ?string $search = null,
        ?string $sort = 'created_desc',
        int $perPage = AppConstants::ADMIN_PAGINATION_LIMIT
    ): LengthAwarePaginator {
        $query = Product::with('category');

        // Filter by category
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Filter by search query (accent or no accent keyword)
        if (!empty($search)) {
            $term = trim($search);
            $query->where(function ($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('material', 'LIKE', "%{$term}%")
                  ->orWhere('color', 'LIKE', "%{$term}%")
                  ->orWhere('description', 'LIKE', "%{$term}%");
            });
        }

        // Apply sorting
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'stock_asc':
                $query->orderBy('stock', 'asc');
                break;
            case 'stock_desc':
                $query->orderBy('stock', 'desc');
                break;
            case 'created_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'created_desc':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get all active products for storefront.
     *
     * @return Collection<int, Product>
     */
    public function getAllActiveProducts(): Collection
    {
        return Product::with('category')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Create a new product.
     *
     * @param array $data
     * @return Product
     */
    public function createProduct(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            if (empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
            }

            // Ensure unique slug
            $originalSlug = $data['slug'];
            $count = 1;
            while (Product::where('slug', $data['slug'])->exists()) {
                $data['slug'] = "{$originalSlug}-{$count}";
                $count++;
            }

            $data['is_featured'] = !empty($data['is_featured']);
            $data['is_active'] = !empty($data['is_active']);

            return Product::create($data);
        });
    }

    /**
     * Update an existing product.
     *
     * @param Product $product
     * @param array $data
     * @return Product
     */
    public function updateProduct(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            if (isset($data['name']) && $data['name'] !== $product->name && empty($data['slug'])) {
                $data['slug'] = Str::slug($data['name']);
                
                $originalSlug = $data['slug'];
                $count = 1;
                while (Product::where('slug', $data['slug'])->where('id', '!=', $product->id)->exists()) {
                    $data['slug'] = "{$originalSlug}-{$count}";
                    $count++;
                }
            }

            $data['is_featured'] = !empty($data['is_featured']);
            $data['is_active'] = !empty($data['is_active']);

            $product->update($data);

            return $product->fresh();
        });
    }

    /**
     * Delete a product.
     *
     * @param Product $product
     * @return bool
     */
    public function deleteProduct(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            return (bool) $product->delete();
        });
    }

    /**
     * Toggle product active status.
     *
     * @param Product $product
     * @return bool
     */
    public function toggleStatus(Product $product): bool
    {
        $product->is_active = !$product->is_active;
        return $product->save();
    }
}
