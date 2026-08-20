<?php

namespace App\Services;

use App\Constants\AppConstants;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryService
{
    /**
     * Lấy toàn bộ danh sách danh mục (mới nhất lên đầu).
     *
     * @return Collection<int, Category>
     */
    public function getAllCategories(): Collection
    {
        return Category::query()
            ->latest('id')
            ->get();
    }

    /**
     * Lấy danh sách danh mục có phân trang.
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedCategories(int $perPage = AppConstants::DEFAULT_PAGINATION_LIMIT): LengthAwarePaginator
    {
        return Category::query()
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Tạo mới danh mục.
     *
     * @param array<string, mixed> $data
     * @return Category
     */
    public function createCategory(array $data): Category
    {
        return Category::create([
            'name' => trim($data['name']),
        ]);
    }

    /**
     * Cập nhật thông tin danh mục.
     *
     * @param Category $category
     * @param array<string, mixed> $data
     * @return bool
     */
    public function updateCategory(Category $category, array $data): bool
    {
        return $category->update([
            'name' => trim($data['name']),
        ]);
    }

    /**
     * Xóa danh mục khỏi hệ thống.
     *
     * @param Category $category
     * @return bool|null
     */
    public function deleteCategory(Category $category): ?bool
    {
        return $category->delete();
    }
}
