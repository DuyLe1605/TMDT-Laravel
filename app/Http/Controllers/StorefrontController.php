<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Product;
use App\Services\BrandService;
use App\Services\CategoryService;
use App\Services\ProductService;
use App\Services\ReviewService;
use App\Services\WishlistService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    /**
     * StorefrontController constructor with Dependency Injection.
     */
    public function __construct(
        protected ProductService $productService,
        protected CategoryService $categoryService,
        protected BrandService $brandService,
        protected ReviewService $reviewService,
        protected WishlistService $wishlistService
    ) {}

    /**
     * Trang chủ Storefront — hiển thị sản phẩm nổi bật.
     */
    public function index(): View
    {
        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->with(['category', 'brand', 'variants'])
            ->latest()
            ->take(8)
            ->get();

        $latestProducts = Product::where('is_active', true)
            ->with(['category', 'brand', 'variants'])
            ->latest()
            ->take(8)
            ->get();

        $categories = $this->categoryService->getCategoryTree();
        $brands = $this->brandService->getAllActiveBrands();
        $wishlistedIds = Auth::check() ? $this->wishlistService->getWishlistedProductIds(Auth::id()) : [];

        return view('storefront.index', compact('featuredProducts', 'latestProducts', 'categories', 'brands', 'wishlistedIds'));
    }

    /**
     * Trang cửa hàng — danh sách sản phẩm với filter danh mục & thương hiệu.
     */
    public function shop(Request $request): View
    {
        $categoryId = $request->filled('category_id') ? (int) $request->input('category_id') : null;
        $brandId = $request->filled('brand_id') ? (int) $request->input('brand_id') : null;
        $search = $request->input('search');
        $sort = $request->input('sort', 'created_desc');
        $minPrice = $request->filled('min_price') ? (float) $request->input('min_price') : null;
        $maxPrice = $request->filled('max_price') ? (float) $request->input('max_price') : null;
        $inStock = $request->input('in_stock');

        $query = Product::where('is_active', true)->with(['category', 'brand', 'variants']);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($brandId) {
            $query->where('brand_id', $brandId);
        }

        if ($inStock === '1' || $inStock === 'true') {
            $query->where('stock', '>', 0);
        }

        if ($minPrice !== null && $minPrice > 0) {
            $query->where(function ($q) use ($minPrice) {
                $q->whereRaw('COALESCE(sale_price, price) >= ?', [$minPrice]);
            });
        }

        if ($maxPrice !== null && $maxPrice > 0) {
            $query->where(function ($q) use ($maxPrice) {
                $q->whereRaw('COALESCE(sale_price, price) <= ?', [$maxPrice]);
            });
        }

        if (!empty($search)) {
            $term = trim($search);
            $unaccentedTerm = \App\Helpers\VietnameseHelper::removeAccents($term);

            $query->where(function ($q) use ($term, $unaccentedTerm) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('search_index', 'like', "%{$unaccentedTerm}%")
                  ->orWhere('sku', 'like', "%{$term}%")
                  ->orWhere('material', 'like', "%{$term}%")
                  ->orWhere('color', 'like', "%{$term}%")
                  ->orWhere('description', 'like', "%{$term}%");
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
        $brands = $this->brandService->getAllActiveBrands();
        $wishlistedIds = Auth::check() ? $this->wishlistService->getWishlistedProductIds(Auth::id()) : [];

        return view('storefront.shop', compact(
            'products', 
            'categories', 
            'brands', 
            'categoryId', 
            'brandId', 
            'search', 
            'sort',
            'minPrice',
            'maxPrice',
            'inStock',
            'wishlistedIds'
        ));
    }

    /**
     * Chi tiết sản phẩm — hiển thị đầy đủ thương hiệu, bộ chọn biến thể thông minh.
     */
    public function show(Product $product): View
    {
        // Only show active products to public
        if (!$product->is_active) {
            abort(404);
        }

        $product->load([
            'category',
            'brand',
            'attributes.values',
            'variants' => function ($q) {
                $q->where('is_active', true);
            }
        ]);

        // Related products from same category or brand
        $relatedProducts = Product::where('is_active', true)
            ->where(function ($q) use ($product) {
                $q->where('category_id', $product->category_id);
                if ($product->brand_id) {
                    $q->orWhere('brand_id', $product->brand_id);
                }
            })
            ->where('id', '!=', $product->id)
            ->with(['category', 'brand'])
            ->take(4)
            ->get();

        // Đánh giá sản phẩm và tổng hợp số sao (Shopee-style)
        $reviewSummary = $this->reviewService->getProductReviewsSummary($product);
        $reviews = $this->reviewService->getFilteredReviews($product, [], 5);
        $isWishlisted = Auth::check() ? $this->wishlistService->isWishlisted(Auth::id(), $product->id) : false;

        return view('storefront.show', compact('product', 'relatedProducts', 'reviewSummary', 'reviews', 'isWishlisted'));
    }

    /**
     * Lấy dữ liệu nhanh của sản phẩm (bao gồm thuộc tính và các biến thể) cho Quick Add Modal.
     */
    public function quickData(Product $product): \Illuminate\Http\JsonResponse
    {
        $product->load([
            'category:id,name',
            'attributes.values',
            'variants' => function ($q) {
                $q->where('is_active', true);
            }
        ]);

        return response()->json([
            'success' => true,
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'image' => $product->image,
                'price' => (float) $product->price,
                'sale_price' => $product->sale_price ? (float) $product->sale_price : null,
                'effective_price' => (float) ($product->sale_price ?? $product->price),
                'has_discount' => $product->has_discount,
                'has_variants' => (bool) $product->has_variants,
                'stock' => (int) $product->stock,
                'category_name' => $product->category?->name ?? 'Túi xách',
                'detail_url' => route('shop.show', $product),
                'attributes' => $product->attributes->map(function ($attr) {
                    return [
                        'id' => $attr->id,
                        'name' => $attr->name,
                        'values' => $attr->values->pluck('value')->values(),
                    ];
                }),
                'variants' => $product->variants->map(function ($v) {
                    return [
                        'id' => $v->id,
                        'sku' => $v->sku,
                        'variant_title' => $v->variant_title,
                        'price' => (float) $v->price,
                        'sale_price' => $v->sale_price ? (float) $v->sale_price : null,
                        'effective_price' => (float) ($v->sale_price ?? $v->price),
                        'stock' => (int) $v->stock,
                        'image' => $v->image,
                        'option1_value' => $v->option1_value,
                        'option2_value' => $v->option2_value,
                        'option3_value' => $v->option3_value,
                    ];
                }),
            ],
        ]);
    }
}
