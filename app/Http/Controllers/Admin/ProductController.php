<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Danh sách sản phẩm rèm
     */
    public function index(Request $request)
    {
        $query = Product::with(['categories', 'colors']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category_id')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        if ($request->filled('stock_status')) {
            $query->where('stock_status', $request->stock_status);
        }

        $products = $query->latest()->paginate(15);
        $categories = Category::all();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Form thêm mới sản phẩm rèm
     */
    public function create()
    {
        $categories = Category::all();
        $colors = Color::active()->get();
        return view('admin.products.create', compact('categories', 'colors'));
    }

    /**
     * Lưu sản phẩm mới
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:50|unique:products,sku',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:unit_price',
            'min_width' => 'nullable|numeric|min:0.1',
            'max_width' => 'nullable|numeric|gt:min_width',
            'min_height' => 'nullable|numeric|min:0.1',
            'max_height' => 'nullable|numeric|gt:min_height',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'material' => 'nullable|string|max:100',
            'style' => 'nullable|string|max:100',
            'curtain_type' => 'nullable|string|max:100',
            'stock_status' => 'required|in:in_stock,out_of_stock,hidden',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'colors' => 'nullable|array',
            'colors.*' => 'exists:colors,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm rèm.',
            'unit_price.required' => 'Vui lòng nhập đơn giá theo m².',
            'unit_price.numeric' => 'Đơn giá phải là dạng số.',
            'sale_price.lt' => 'Giá khuyến mãi phải nhỏ hơn đơn giá gốc.',
            'categories.required' => 'Vui lòng chọn ít nhất một danh mục.',
            'main_image.image' => 'Ảnh đại diện không hợp lệ.',
        ]);

        $validated['slug'] = $validated['slug'] ? Str::slug($validated['slug']) : Str::slug($validated['name']);
        $validated['sku'] = $validated['sku'] ?: 'REM-' . strtoupper(Str::random(6));
        $validated['is_hot'] = $request->has('is_hot');
        $validated['is_trending'] = $request->has('is_trending');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_new'] = $request->has('is_new');

        if ($request->hasFile('main_image')) {
            $validated['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        $product = Product::create($validated);

        // Gán danh mục
        $product->categories()->sync($request->categories);

        // Gán màu sắc & tồn kho
        if ($request->filled('colors')) {
            $colorData = [];
            foreach ($request->colors as $colorId) {
                $stock = $request->input("color_stock.{$colorId}", 50);
                $colorData[$colorId] = ['stock_quantity' => $stock];
            }
            $product->colors()->sync($colorData);
        }

        // Upload thư viện ảnh phụ
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $idx => $imgFile) {
                $path = $imgFile->store('products/gallery', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'sort_order' => $idx + 1,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Thêm mới sản phẩm rèm thành công!');
    }

    /**
     * Form sửa sản phẩm
     */
    public function edit(Product $product)
    {
        $categories = Category::all();
        $colors = Color::active()->get();
        $product->load(['categories', 'colors', 'images']);

        return view('admin.products.edit', compact('product', 'categories', 'colors'));
    }

    /**
     * Cập nhật sản phẩm
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'slug' => 'required|string|max:255|unique:products,slug,' . $product->id,
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lt:unit_price',
            'min_width' => 'nullable|numeric|min:0.1',
            'max_width' => 'nullable|numeric|gt:min_width',
            'min_height' => 'nullable|numeric|min:0.1',
            'max_height' => 'nullable|numeric|gt:min_height',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'material' => 'nullable|string|max:100',
            'style' => 'nullable|string|max:100',
            'curtain_type' => 'nullable|string|max:100',
            'stock_status' => 'required|in:in_stock,out_of_stock,hidden',
            'categories' => 'required|array|min:1',
            'categories.*' => 'exists:categories,id',
            'colors' => 'nullable|array',
            'colors.*' => 'exists:colors,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $validated['slug'] = Str::slug($validated['slug']);
        $validated['is_hot'] = $request->has('is_hot');
        $validated['is_trending'] = $request->has('is_trending');
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_new'] = $request->has('is_new');

        if ($request->hasFile('main_image')) {
            if ($product->main_image && !Str::startsWith($product->main_image, ['http://', 'https://'])) {
                Storage::disk('public')->delete($product->main_image);
            }
            $validated['main_image'] = $request->file('main_image')->store('products', 'public');
        }

        $product->update($validated);

        // Sync danh mục
        $product->categories()->sync($request->categories);

        // Sync màu sắc & tồn kho
        $colorData = [];
        if ($request->filled('colors')) {
            foreach ($request->colors as $colorId) {
                $stock = $request->input("color_stock.{$colorId}", 50);
                $colorData[$colorId] = ['stock_quantity' => $stock];
            }
        }
        $product->colors()->sync($colorData);

        // Thêm ảnh phụ mới nếu có tải lên
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $idx => $imgFile) {
                $path = $imgFile->store('products/gallery', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'sort_order' => $product->images()->count() + $idx + 1,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật thông tin rèm thành công!');
    }

    /**
     * Xóa sản phẩm (Soft Delete)
     */
    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Đã chuyển sản phẩm vào thùng rác.');
    }
}
