<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * Danh sách sản phẩm rèm yêu thích
     */
    public function index()
    {
        $wishlists = Auth::user()->wishlists()->with('product.categories')->latest('created_at')->paginate(12);

        return view('wishlist.index', compact('wishlists'));
    }

    /**
     * Thêm hoặc bỏ thích sản phẩm rèm
     */
    public function toggle(Product $product, Request $request)
    {
        $userId = Auth::id();
        $existing = Wishlist::where('user_id', $userId)->where('product_id', $product->id)->first();

        if ($existing) {
            $existing->delete();
            $isWishlisted = false;
            $message = 'Đã bỏ sản phẩm khỏi danh sách yêu thích.';
        } else {
            Wishlist::create([
                'user_id' => $userId,
                'product_id' => $product->id,
            ]);
            $isWishlisted = true;
            $message = 'Đã thêm rèm ' . $product->name . ' vào danh sách yêu thích!';
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_wishlisted' => $isWishlisted,
                'message' => $message,
                'wishlist_count' => Auth::user()->wishlists()->count(),
            ]);
        }

        return back()->with('success', $message);
    }
}
