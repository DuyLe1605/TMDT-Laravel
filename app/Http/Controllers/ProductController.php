<?php

namespace App\Http\Controllers;

use App\Constants\AppConstants;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * ProductController constructor with Dependency Injection.
     *
     * @param ProductService $productService
     * @param CategoryService $categoryService
     */
    public function __construct(
        protected ProductService $productService,
        protected CategoryService $categoryService
    ) {}

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $categoryId = $request->filled('category_id') ? (int) $request->input('category_id') : null;
        $search = $request->input('search');
        $sort = $request->input('sort', 'created_desc');

        $products = $this->productService->getPaginatedProducts($categoryId, $search, $sort);
        $categories = $this->categoryService->getAllCategories();

        return view('products.index', compact('products', 'categories', 'categoryId', 'search', 'sort'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create(): View
    {
        $categories = $this->categoryService->getAllCategories();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreProductRequest $request
     * @return RedirectResponse
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->productService->createProduct($request->validated());

        return redirect()
            ->route('admin.products.index')
            ->with(AppConstants::FLASH_SUCCESS, AppConstants::MSG_PRODUCT_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return View
     */
    public function show(Product $product): View
    {
        $product->load('category');
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Product $product
     * @return View
     */
    public function edit(Product $product): View
    {
        $categories = $this->categoryService->getAllCategories();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateProductRequest $request
     * @param Product $product
     * @return RedirectResponse
     */
    public function update(UpdateProductRequest $request, Product $product): RedirectResponse
    {
        $this->productService->updateProduct($product, $request->validated());

        return redirect()
            ->route('admin.products.index')
            ->with(AppConstants::FLASH_SUCCESS, AppConstants::MSG_PRODUCT_UPDATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
    {
        $this->productService->deleteProduct($product);

        return redirect()
            ->route('admin.products.index')
            ->with(AppConstants::FLASH_SUCCESS, AppConstants::MSG_PRODUCT_DELETED);
    }
}
