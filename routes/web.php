<?php

use App\Http\Controllers\AccountAddressController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountOrderController;
use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminContactMessageController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLanguageController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminTranslationController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderConfirmationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public routes definition (reused for default and prefixed locales)
$publicRoutes = function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/pages/{page:slug}', [PageController::class, 'show'])->name('pages.show');
    Route::get('/about', fn () => app(PageController::class)->show(\App\Models\Page::where('slug', 'about')->firstOrFail()))->name('about');
    Route::get('/contact', [ContactController::class, 'show'])->name('contact');
    Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');

    // Cart routes
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{productId}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{productId}', [CartController::class, 'remove'])->name('cart.remove');

    // Checkout routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Order confirmation
    Route::get('/orders/{order}/confirmation', [OrderConfirmationController::class, 'show'])->name('orders.confirmation');
};

// 1. Default locale (no prefix)
Route::middleware('set-locale')->group($publicRoutes);

// 2. Non-default locales (/{locale}/...)
Route::prefix('{locale}')
    ->where(['locale' => '[a-z]{2}'])
    ->middleware('set-locale')
    ->group($publicRoutes);

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/locale', [ProfileController::class, 'updateLocale'])->name('locale.update');
});

// Admin routes
Route::middleware(['auth', 'admin', 'set-admin-locale'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('categories', AdminCategoryController::class)->except('show');
    Route::resource('products', AdminProductController::class)->except('show');

    Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}', [AdminOrderController::class, 'update'])->name('orders.update');

    Route::resource('pages', AdminPageController::class)->except('show');

    Route::get('settings', [AdminSettingController::class, 'edit'])->name('settings.edit');
    Route::post('settings', [AdminSettingController::class, 'update'])->name('settings.update');

    Route::get('contact-messages', [AdminContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::get('contact-messages/{message}', [AdminContactMessageController::class, 'show'])->name('contact-messages.show');
    Route::delete('contact-messages/{message}', [AdminContactMessageController::class, 'destroy'])->name('contact-messages.destroy');

    Route::get('users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('users/{user}', [AdminUserController::class, 'show'])->name('users.show');
    Route::patch('users/{user}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

    Route::resource('languages', AdminLanguageController::class)->except('show');
    Route::get('translations', [AdminTranslationController::class, 'index'])->name('translations.index');
    Route::post('translations', [AdminTranslationController::class, 'update'])->name('translations.update');
});

// Account routes
Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/', [AccountController::class, 'index'])->name('index');
    Route::get('orders', [AccountOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [AccountOrderController::class, 'show'])->name('orders.show');
    Route::get('addresses', [AccountAddressController::class, 'edit'])->name('addresses.edit');
    Route::post('addresses', [AccountAddressController::class, 'update'])->name('addresses.update');
});

require __DIR__.'/auth.php';
