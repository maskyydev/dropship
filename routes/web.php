<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\VerificationNoticeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\Admin\AdminPanelController;
use App\Http\Controllers\Admin\VerificationController;
use App\Http\Controllers\Auth\EmailVerificationController; // Tambahkan ini
use App\Http\Controllers\ProductJualController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ShippingController;

// Homepage
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products/{product}', [HomeController::class, 'showProduct'])->name('products.show');
Route::post('/jual/{product}', [ProductController::class, 'jual'])->middleware('auth')->name('product.jual');
Route::post('/wishlist/{product}', [ProductController::class, 'wishlist'])->middleware('auth')->name('product.wishlist');

// Route untuk proses beli
Route::middleware('auth')->group(function () {
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
});

Route::middleware(['auth'])->group(function () {
    // Checkout routes
    Route::post('/checkout/process', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    
    // Midtrans notification handler
    Route::post('/checkout/notification', [CheckoutController::class, 'notification'])->name('checkout.notification');
});

// Daftar Produk
Route::middleware(['auth'])->group(function () {
    Route::get('/produk-saya', [ProductJualController::class, 'index'])
        ->name('produk.daftar');
});
Route::post('/produk/hapus-terpilih', [ProductJualController::class, 'bulkDelete'])
    ->middleware('auth')
    ->name('produk.bulk-delete');

// Daftar Wishlist
Route::middleware(['auth'])->group(function () {
    Route::get('/wishlist', [ProductJualController::class, 'wishlist'])
        ->name('produk.wishlist');
});
Route::delete('/wishlist/hapus/{id}', [ProductJualController::class, 'hapusWishlist'])->middleware('auth')->name('produk.hapus');

// Pembayaran User(seller)
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout-barang/{id}', [PembayaranController::class, 'checkoutPage'])->name('pembayaran.checkout');
    Route::post('/checkout-barang', [PembayaranController::class, 'checkout'])->name('checkout.barang');
    Route::get('/pembayaran/finish', [PembayaranController::class, 'finish'])->name('pembayaran.finish');
    Route::get('/pembayaran/unfinish', [PembayaranController::class, 'unfinish'])->name('pembayaran.unfinish');
    Route::get('/pembayaran/error', [PembayaranController::class, 'error'])->name('pembayaran.error');
});
Route::middleware(['auth'])->group(function () {
    Route::post('/pembayaran/get-redirect-url', [PembayaranController::class, 'getRedirectUrl'])->name('pembayaran.getRedirectUrl');
    // Midtrans notification handler
    Route::post('/pembayaran/notification', [PembayaranController::class, 'notificationHandler']);
});

// Shipping
// Route::post('/checkout/barang', [ShippingController::class, 'store'])->name('checkout.barang')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    // Registration Routes
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
    
    // Email Verification Routes
    Route::post('/verify-code', [EmailVerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/verification/resend', [EmailVerificationController::class, 'resend'])->name('verification.resend');
});

// Logout (accessible when authenticated)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    // Payment Routes
    Route::get('/pricing', [PaymentController::class, 'pricing'])->name('pricing');
    Route::post('/checkout', [PaymentController::class, 'checkout'])->name('checkout');
    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/failed', [PaymentController::class, 'failed'])->name('payment.failed');
    Route::post('/payment/notification', [PaymentController::class, 'notification'])->name('payment.notification');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    // Admin Verification Notice (accessible without full auth)
    Route::get('/verification-notice', [VerificationNoticeController::class, 'show'])
        ->name('verification.notice');
    
    // Admin Verification Routes (only needs auth + admin role)
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/unverified-admins', [VerificationController::class, 'unverifiedAdmins'])
            ->name('verification.index');
        Route::post('/verify-admin/{admin}', [VerificationController::class, 'verifyAdmin'])
            ->name('verification.verify');
        Route::post('/resend-verification/{admin}', [VerificationController::class, 'resendVerification'])
            ->name('verification.resend');
    });
    
    // Full Admin Routes (requires auth + admin role + verification)
    Route::middleware(['auth', 'admin', 'verified.admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Products Management
        Route::resource('products', ProductController::class)->except(['show']);
        // Untuk soft delete
        Route::get('products/trash', [ProductController::class, 'trash'])->name('products.trash');
        Route::post('products/{id}/restore', [ProductController::class, 'restore'])->name('products.restore');
        Route::delete('products/{id}/force-delete', [ProductController::class, 'forceDelete'])->name('products.force-delete');
        
         // Sales Management
        Route::get('/sales', [SalesController::class, 'index'])->name('sales.index');
        Route::get('/sales/create', [SalesController::class, 'create'])->name('sales.create');
        Route::post('/sales', [SalesController::class, 'store'])->name('sales.store');
        Route::get('/sales/{sale}', [SalesController::class, 'show'])->name('sales.show');
        Route::delete('/sales/{sale}', [SalesController::class, 'destroy'])->name('sales.destroy');
        
        // User Management
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
        
        // Additional Admin Panel Routes
        // Route::get('/users/{user}/edit', [AdminPanelController::class, 'editUser'])->name('users.edit');
        // Route::put('/users/{user}', [AdminPanelController::class, 'updateUser'])->name('users.update');
        // Route::delete('/users/{user}', [AdminPanelController::class, 'deleteUser'])->name('users.delete');
    });
});