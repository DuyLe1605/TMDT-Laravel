<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Product;

class StorefrontController extends Controller
{
    /**
     * StorefrontController constructor with Dependency Injection.
     */
    public function __construct(
        protected ProductService $productService,
        protected CategoryService $categoryService
    ) {}

    /**
     * Trang chủ Storefront — hiển thị sản phẩm nổi bật.
     */
    public function index(): View
    {
        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        $latestProducts = Product::where('is_active', true)
            ->with('category')
            ->latest()
            ->take(8)
            ->get();

        $categories = $this->categoryService->getAllCategories();

        return view('storefront.index', compact('featuredProducts', 'latestProducts', 'categories'));
    }

    /**
     * Trang cửa hàng — danh sách sản phẩm với filter.
     */
    public function shop(Request $request): View
    {
        $categoryId = $request->filled('category_id') ? (int) $request->input('category_id') : null;
        $search = $request->input('search');
        $sort = $request->input('sort', 'created_desc');

        $query = Product::where('is_active', true)->with('category');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('material', 'like', "%{$search}%")
                  ->orWhere('color', 'like', "%{$search}%");
            });
        }

        switch ($sort) {
            case 'price_asc':
                $query->orderByRaw('COALESCE(sale_price, price) ASC');
                break;
            case 'price_desc':
                $query->orderByRaw('COALESCE(sale_price, price) DESC');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'created_asc':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = $this->categoryService->getAllCategories();

        return view('storefront.shop', compact('products', 'categories', 'categoryId', 'search', 'sort'));
    }

    /**
     * Chi tiết sản phẩm.
     */
    public function show(Product $product): View
    {
        // Only show active products to public
        if (!$product->is_active) {
            abort(404);
        }

        $product->load('category');

        // Related products from same category
        $relatedProducts = Product::where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('storefront.show', compact('product', 'relatedProducts'));
    }
}
