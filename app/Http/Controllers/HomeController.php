<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Danh mục nổi bật trang chủ
        $homeCategories = Category::active()
            ->onHome()
            ->withCount('products')
            ->orderBy('sort_order', 'asc')
            ->take(6)
            ->get();

        // 2. Sản phẩm HOT
        $hotProducts = Product::active()
            ->hot()
            ->with(['categories', 'colors'])
            ->take(4)
            ->get();

        // 3. Sản phẩm Thịnh Hành (Trending)
        $trendingProducts = Product::active()
            ->trending()
            ->with(['categories', 'colors'])
            ->take(4)
            ->get();

        // 4. Sản phẩm Khuyến Mãi (Có sale_price)
        $saleProducts = Product::active()
            ->whereNotNull('sale_price')
            ->with(['categories', 'colors'])
            ->take(4)
            ->get();

        // 5. Sản phẩm Mới
        $newProducts = Product::active()
            ->new()
            ->with(['categories', 'colors'])
            ->latest()
            ->take(8)
            ->get();

        return view('home', compact(
            'homeCategories',
            'hotProducts',
            'trendingProducts',
            'saleProducts',
            'newProducts'
        ));
    }
}
