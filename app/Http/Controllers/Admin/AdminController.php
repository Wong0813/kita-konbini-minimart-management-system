<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use App\Models\Order;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalProducts  = Product::count();
        $totalCategories = Category::count();
        $totalUsers     = User::where('is_admin', false)->count();
        $totalOrders    = Order::count();
        $totalRevenue   = Order::sum('total');
        $lowStock       = Product::where('stock', '<=', 3)->count();
        $expiringSoon   = Product::whereNotNull('expiry_date')
                            ->whereDate('expiry_date', '<=', now()->addDays(7))
                            ->whereDate('expiry_date', '>=', now())
                            ->count();
        $expired        = Product::whereNotNull('expiry_date')
                            ->whereDate('expiry_date', '<', now())
                            ->count();
        $recentOrders   = Order::with('user')->latest()->take(5)->get();
        $monthlySales   = Order::selectRaw('MONTH(created_at) as month, SUM(total) as total')
                            ->whereYear('created_at', now()->year)
                            ->groupBy('month')
                            ->orderBy('month')
                            ->get();

        return view('admin.dashboard', compact(
            'totalProducts', 'totalCategories', 'totalUsers',
            'totalOrders', 'totalRevenue', 'lowStock',
            'expiringSoon', 'expired', 'recentOrders', 'monthlySales'
        ));
    }

    public function revenue()
{
    $orders = Order::with('user', 'items.product')->latest()->get();
    $totalRevenue = Order::sum('total');
    $totalOrders  = Order::count();
    $monthlySales = Order::selectRaw('MONTH(created_at) as month, SUM(total) as total, COUNT(*) as count')
                        ->whereYear('created_at', now()->year)
                        ->groupBy('month')
                        ->orderBy('month')
                        ->get();

    return view('admin.revenue', compact('orders', 'totalRevenue', 'totalOrders', 'monthlySales'));
}
}