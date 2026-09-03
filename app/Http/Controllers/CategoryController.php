<?php

namespace App\Http\Controllers;

use App\Constants\AppConstants;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $categories = $this->categoryService->getPaginatedCategories($search);
        return view('categories.index', compact('categories', 'search'));
    }

    /**
     * Hiển thị form tạo mới danh mục.
     */
    public function create(): View
    {
        $parentCategories = Category::roots()->orderBy('name')->get();
        return view('categories.create', compact('parentCategories'));
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
        $category->load(['parent', 'children', 'products']);
        return view('categories.show', compact('category'));
    }

    /**
     * Hiển thị form chỉnh sửa danh mục.
     */
    public function edit(Category $category): View
    {
        $parentCategories = Category::roots()->where('id', '!=', $category->id)->orderBy('name')->get();
        return view('categories.edit', compact('category', 'parentCategories'));
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
