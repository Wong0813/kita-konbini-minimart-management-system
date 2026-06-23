<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShelfController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Web Routes - Kita Konbini Minimart
|--------------------------------------------------------------------------
*/

// ─── Auth Routes (Guest only) ────────────────────────────────────────────────

Route::middleware('guest')->group(function () {

    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');

});

// ─── Authenticated Routes ────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Home
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Products — specific routes MUST come before {id} wildcard
    Route::get('/products',                    [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/search',             [ProductController::class, 'search'])->name('products.search');
    Route::get('/products/catalogue',          [ProductController::class, 'catalogue'])->name('products.catalogue');
    Route::get('/products/combo/{id}',         [ProductController::class, 'combo'])->name('products.combo');
    Route::get('/products/{id}',               [ProductController::class, 'show'])->name('products.show');

    // Category
    Route::get('/category/{slug}',             [ProductController::class, 'category'])->name('products.category');

    // Cart
    Route::get('/cart',                        [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{id}',              [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{id}',           [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{id}',         [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/checkout',              [CartController::class, 'checkout'])->name('cart.checkout');

    // Wishlist
    Route::get('/wishlist',                    [ProductController::class, 'wishlist'])->name('wishlist');
    Route::post('/wishlist/toggle/{id}',       [ProductController::class, 'toggleWishlist'])->name('wishlist.toggle');

    // Shelf Locator
    Route::get('/shelf',                       [ShelfController::class, 'index'])->name('shelf.index');
    Route::get('/shelf/slot/{code}',           [ShelfController::class, 'slotInfo'])->name('shelf.slot');

    // Profile
    Route::get('/profile',                     [ProfileController::class, 'show'])->name('profile');
    Route::post('/profile',                    [ProfileController::class, 'update'])->name('profile.update');

    // Notifications
Route::get('/notifications',          [NotificationController::class, 'index'])->name('notifications.index');
Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readall');
Route::get('/notifications/count',    [NotificationController::class, 'unreadCount'])->name('notifications.count');

    // ─── Admin Routes ─────────────────────────────────────────────────────────

    Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

        // Dashboard
        Route::get('/', [\App\Http\Controllers\Admin\AdminController::class, 'dashboard'])->name('dashboard');

        // Products
        Route::get('/products',                    [\App\Http\Controllers\Admin\AdminProductController::class, 'index'])->name('products.index');
        Route::get('/products/create',             [\App\Http\Controllers\Admin\AdminProductController::class, 'create'])->name('products.create');
        Route::post('/products',                   [\App\Http\Controllers\Admin\AdminProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit',     [\App\Http\Controllers\Admin\AdminProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}',          [\App\Http\Controllers\Admin\AdminProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}',       [\App\Http\Controllers\Admin\AdminProductController::class, 'destroy'])->name('products.destroy');

        // Inventory
        Route::get('/inventory',                   [\App\Http\Controllers\Admin\AdminProductController::class, 'inventory'])->name('inventory');
        Route::post('/inventory/{product}/stock',  [\App\Http\Controllers\Admin\AdminProductController::class, 'adjustStock'])->name('inventory.adjust');

        // Categories
        Route::get('/categories',                  [\App\Http\Controllers\Admin\AdminCategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories',                 [\App\Http\Controllers\Admin\AdminCategoryController::class, 'store'])->name('categories.store');
        Route::delete('/categories/{category}',    [\App\Http\Controllers\Admin\AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        // Users
        Route::get('/users',                       [\App\Http\Controllers\Admin\AdminUserController::class, 'index'])->name('users.index');
        Route::delete('/users/{user}',             [\App\Http\Controllers\Admin\AdminUserController::class, 'destroy'])->name('users.destroy');

        // Revenue
        Route::get('/revenue',                     [\App\Http\Controllers\Admin\AdminController::class, 'revenue'])->name('revenue');

        // Shelf Management
        Route::get('/shelf',                       [\App\Http\Controllers\Admin\AdminShelfController::class, 'index'])->name('shelf.manage');
        Route::post('/shelf/assign',               [\App\Http\Controllers\Admin\AdminShelfController::class, 'assign'])->name('shelf.assign');
        Route::post('/shelf/clear',                [\App\Http\Controllers\Admin\AdminShelfController::class, 'clear'])->name('shelf.clear');

        Route::post('/products/{product}/restock', [\App\Http\Controllers\Admin\AdminProductController::class, 'restock'])->name('products.restock');

    });

});