<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\View\View;

class AdminController extends Controller
{
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

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalCategories',
            'activeProducts',
            'outOfStock',
            'featuredCount',
            'latestProducts'
        ));
    }
}
