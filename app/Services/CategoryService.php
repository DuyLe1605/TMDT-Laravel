<?php

namespace App\Services;

use App\Constants\AppConstants;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class CategoryService
{
    /**
     * Lấy toàn bộ danh sách danh mục (kèm quan hệ cha).
     *
     * @return Collection<int, Category>
     */
    public function getAllCategories(): Collection
    {
        return Category::query()
            ->with(['parent', 'children'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Lấy danh mục cấp gốc (Root categories) kèm danh mục con.
     *
     * @return Collection<int, Category>
     */
    public function getCategoryTree(): Collection
    {
        return Category::roots()
            ->active()
            ->with(['children' => function ($q) {
                $q->active()->withCount('products');
            }])
            ->withCount('products')
            ->orderBy('name')
            ->get();
    }

    /**
     * Lấy danh sách danh mục có phân trang kèm tìm kiếm.
     *
     * @param string|null $search
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedCategories(?string $search = null, int $perPage = AppConstants::ADMIN_PAGINATION_LIMIT): LengthAwarePaginator
    {
        $query = Category::with(['parent'])->withCount('products');

        if (!empty($search)) {
            $term = trim($search);
            $query->where(function ($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('description', 'LIKE', "%{$term}%");
            });
        }

        return $query->orderByRaw('COALESCE(parent_id, id), parent_id IS NOT NULL, id ASC')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Tạo mới danh mục.
     *
     * @param array<string, mixed> $data
     * @return Category
     */
    public function createCategory(array $data): Category
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $originalSlug = $data['slug'];
        $count = 1;
        while (Category::where('slug', $data['slug'])->exists()) {
            $data['slug'] = "{$originalSlug}-{$count}";
            $count++;
        }

        $parentId = !empty($data['parent_id']) ? (int) $data['parent_id'] : null;

        return Category::create([
            'parent_id' => $parentId,
            'name' => trim($data['name']),
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'image' => $data['image'] ?? null,
            'is_active' => !empty($data['is_active']),
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
        if (isset($data['name']) && $data['name'] !== $category->name && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
            $originalSlug = $data['slug'];
            $count = 1;
            while (Category::where('slug', $data['slug'])->where('id', '!=', $category->id)->exists()) {
                $data['slug'] = "{$originalSlug}-{$count}";
                $count++;
            }
        }

        $parentId = !empty($data['parent_id']) ? (int) $data['parent_id'] : null;

        // Prevent setting itself or its descendants as its own parent
        if ($parentId === $category->id) {
            $parentId = null;
        }

        return $category->update([
            'parent_id' => $parentId,
            'name' => trim($data['name']),
            'slug' => $data['slug'] ?? $category->slug,
            'description' => $data['description'] ?? null,
            'image' => $data['image'] ?? null,
            'is_active' => !empty($data['is_active']),
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
