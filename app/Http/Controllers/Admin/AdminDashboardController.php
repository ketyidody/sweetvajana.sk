<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'totalOrders' => Order::count(),
                'totalRevenue' => Order::sum('total'),
                'totalProducts' => Product::count(),
                'totalCategories' => Category::count(),
                'totalUsers' => User::count(),
            ],
            'recentOrders' => Order::with('user')
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }
}
