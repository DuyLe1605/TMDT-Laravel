<?php

namespace App\Services;

use App\Constants\AppConstants;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class BrandService
{
    /**
     * Get all active brands.
     *
     * @return Collection<int, Brand>
     */
    public function getAllActiveBrands(): Collection
    {
        return Brand::active()->orderBy('name')->get();
    }

    /**
     * Get all brands for admin select.
     *
     * @return Collection<int, Brand>
     */
    public function getAllBrands(): Collection
    {
        return Brand::orderBy('name')->get();
    }

    /**
     * Get paginated brands with search.
     *
     * @param string|null $search
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedBrands(?string $search = null, int $perPage = AppConstants::ADMIN_PAGINATION_LIMIT): LengthAwarePaginator
    {
        $query = Brand::withCount('products');

        if (!empty($search)) {
            $term = trim($search);
            $query->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('description', 'LIKE', "%{$term}%");
        }

        return $query->latest('id')->paginate($perPage)->withQueryString();
    }

    /**
     * Create brand.
     *
     * @param array $data
     * @return Brand
     */
    public function createBrand(array $data): Brand
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $originalSlug = $data['slug'];
        $count = 1;
        while (Brand::where('slug', $data['slug'])->exists()) {
            $data['slug'] = "{$originalSlug}-{$count}";
            $count++;
        }

        $data['is_active'] = !empty($data['is_active']);

        return Brand::create($data);
    }

    /**
     * Update brand.
     *
     * @param Brand $brand
     * @param array $data
     * @return bool
     */
    public function updateBrand(Brand $brand, array $data): bool
    {
        if (isset($data['name']) && $data['name'] !== $brand->name && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
            $originalSlug = $data['slug'];
            $count = 1;
            while (Brand::where('slug', $data['slug'])->where('id', '!=', $brand->id)->exists()) {
                $data['slug'] = "{$originalSlug}-{$count}";
                $count++;
            }
        }

        $data['is_active'] = !empty($data['is_active']);

        return $brand->update($data);
    }

    /**
     * Delete brand.
     *
     * @param Brand $brand
     * @return bool|null
     */
    public function deleteBrand(Brand $brand): ?bool
    {
        return $brand->delete();
    }
}
