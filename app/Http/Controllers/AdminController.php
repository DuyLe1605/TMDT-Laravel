<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Hiển thị trang Dashboard quản trị.
     */
    public function dashboard(): View
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();
        $activeProducts = Product::where('is_active', true)->count();
        $outOfStock = Product::where('stock', '<=', 0)->count();
        $featuredCount = Product::where('is_featured', true)->count();
        $latestProducts = Product::with('category')->latest()->take(5)->get();

        // Order metrics & pending list
        $orderStats = $this->orderService->getOrderStatistics();
        $recentOrders = Order::with('items')->latest()->take(5)->get();
        $pendingOrders = Order::where('shipping_status', Order::STATUS_PENDING)->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCategories',
            'activeProducts',
            'outOfStock',
            'featuredCount',
            'latestProducts',
            'orderStats',
            'recentOrders',
            'pendingOrders'
        ));
    }
}
