<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products' => Product::count(),
            'total_categories' => Category::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'total_revenue' => Order::where(function ($q) {
                $q->where('status', 'completed')
                  ->orWhere('payment_status', 'paid');
            })->sum('total'),
        ];

        $recentProducts = Product::with('categories')->latest()->take(5)->get();
        $recentOrders = Order::with(['items.product', 'user'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentProducts', 'recentOrders'));
    }
}
