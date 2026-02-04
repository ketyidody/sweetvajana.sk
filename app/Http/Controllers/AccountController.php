<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class AccountController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return Inertia::render('Account/Index', [
            'recentOrders' => $user->orders()->latest()->take(5)->get(),
        ]);
    }
}
