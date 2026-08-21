<?php

namespace App\Http\Controllers;

use App\Constants\AppConstants;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class CategoryController extends Controller
{
    /**
     * Khởi tạo Controller với Service Injection.
     */
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    /**
     * Hiển thị danh sách các danh mục.
     */
    public function index(): View
    {
        $categories = $this->categoryService->getPaginatedCategories();
        return view('categories.index', compact('categories'));
    }

    /**
     * Hiển thị form tạo mới danh mục.
     */
    public function create(): View
    {
        return view('categories.create');
    }

    /**
     * Xử lý lưu danh mục mới vào cơ sở dữ liệu.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $this->categoryService->createCategory($request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with(AppConstants::FLASH_SUCCESS, AppConstants::MSG_CATEGORY_CREATED);
    }

    /**
     * Hiển thị thông tin chi tiết của danh mục.
     */
    public function show(Category $category): View
    {
        return view('categories.show', compact('category'));
    }

    /**
     * Hiển thị form chỉnh sửa danh mục.
     */
    public function edit(Category $category): View
    {
        return view('categories.edit', compact('category'));
    }

    /**
     * Xử lý cập nhật thông tin danh mục.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $this->categoryService->updateCategory($category, $request->validated());

        return redirect()
            ->route('admin.categories.index')
            ->with(AppConstants::FLASH_SUCCESS, AppConstants::MSG_CATEGORY_UPDATED);
    }

    /**
     * Xử lý xóa danh mục khỏi hệ thống.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $this->categoryService->deleteCategory($category);

        return redirect()
            ->route('admin.categories.index')
            ->with(AppConstants::FLASH_SUCCESS, AppConstants::MSG_CATEGORY_DELETED);
    }
}
