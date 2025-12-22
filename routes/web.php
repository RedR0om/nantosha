<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\HowToOrderController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Products
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/best-sellers', [ProductController::class, 'bestSellers'])->name('products.best-sellers');
Route::get('/products/new-arrivals', [ProductController::class, 'newArrivals'])->name('products.new-arrivals');
Route::get('/products/{slug}', [ProductController::class, 'show'])->name('products.show');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
Route::put('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');

// Checkout
Route::get('/checkout', [\App\Http\Controllers\CheckoutController::class, 'index'])->name('checkout');

// Orders
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// Information Pages
Route::get('/how-to-order', [HowToOrderController::class, 'index'])->name('how-to-order');
Route::get('/faq', [FAQController::class, 'index'])->name('faq');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Dashboard
Route::get('dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('products', AdminProductController::class);
    Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class);
    Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('carousel', \App\Http\Controllers\Admin\CarouselController::class);
    Route::resource('faqs', \App\Http\Controllers\Admin\FaqController::class);
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');
    Route::put('inventory/{product}', [InventoryController::class, 'update'])->name('inventory.update');
    Route::post('inventory/bulk-update', [InventoryController::class, 'bulkUpdate'])->name('inventory.bulk-update');
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
    Route::get('orders', [\App\Http\Controllers\Admin\AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [\App\Http\Controllers\Admin\AdminOrderController::class, 'show'])->name('orders.show');
    Route::put('orders/{order}', [\App\Http\Controllers\Admin\AdminOrderController::class, 'update'])->name('orders.update');
});

require __DIR__.'/settings.php';
