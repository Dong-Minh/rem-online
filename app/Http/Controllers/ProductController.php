<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Danh sách sản phẩm rèm (Cửa hàng / Tìm kiếm / Lọc)
     */
    public function index(Request $request)
    {
        $categories = Category::active()->withCount('products')->get();
        $colors = Color::active()->get();
        
        $materials = Product::active()->whereNotNull('material')->distinct()->pluck('material');
        $styles = Product::active()->whereNotNull('style')->distinct()->pluck('style');
        $curtainTypes = Product::active()->whereNotNull('curtain_type')->distinct()->pluck('curtain_type');

        $products = Product::active()
            ->with(['categories', 'colors'])
            ->filter($request->all())
            ->paginate(12)
            ->withQueryString();

        $currentCategory = null;
        if ($request->filled('category')) {
            $currentCategory = Category::where('slug', $request->category)->first();
        }

        return view('products.index', compact(
            'products',
            'categories',
            'colors',
            'materials',
            'styles',
            'curtainTypes',
            'currentCategory'
        ));
    }

    /**
     * Chi tiết sản phẩm rèm
     */
    public function show($slug)
    {
        $product = Product::active()
            ->with(['categories', 'colors', 'images', 'reviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Tăng lượt xem
        $product->increment('view_count');

        // Sản phẩm liên quan
        $relatedProducts = Product::active()
            ->where('id', '!=', $product->id)
            ->whereHas('categories', function ($q) use ($product) {
                $q->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
