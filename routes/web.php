<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductCategoryController;

// Pages
Route::get('/', function () { return view('home'); })->name('home');
Route::get('/risale-i-nur', function () { return view('risale'); })->name('risale');
Route::get('/said-nursi', function () { return view('saidnursi'); })->name('saidnursi');
Route::get('/contact', function () { return view('contact'); })->name('contact');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// Profile
Route::get('/dashboard/profile', [ProfileController::class, 'editPage'])->name('editProfile')->middleware('auth');
Route::post('/dashboard/profile', [ProfileController::class, 'updateProfile'])->name('updateProfile')->middleware('auth');

// Auth pages
Route::get('/register', [AuthController::class, 'registerPage'])->name('register')->middleware('guest');
Route::post('/register', [AuthController::class, 'registerUser'])->name('registerUser')->middleware('guest');
Route::get('/login', [AuthController::class, 'loginPage'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'loginUser'])->name('loginUser')->middleware('guest');
Route::get('/logout', [AuthController::class, 'logoutGet'])->name('logout_get')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Products
Route::get('/dashboard/products', [ProductController::class, 'index'])->name('productIndex')->middleware('auth');
Route::get('/dashboard/products/create', [ProductController::class, 'create'])->name('productCreatePage')->middleware('auth');
Route::post('/dashboard/products/create', [ProductController::class, 'store'])->name('productStore')->middleware('auth');
Route::get('/dashboard/products/edit/{id}', [ProductController::class, 'edit'])->name('productEditPage')->middleware('auth');
Route::put('/dashboard/products/edit/{id}', [ProductController::class, 'update'])->name('productUpdate')->middleware('auth');
Route::delete('/dashboard/products/delete/{id}', [ProductController::class, 'destroy'])->name('productDelete')->middleware('auth');

// Productcategories
Route::get('/dashboard/productcategories', [ProductCategoryController::class, 'index'])->name('productCategoryIndex')->middleware('auth');
Route::get('/dashboard/productcategories/create', [ProductCategoryController::class, 'create'])->name('productCategoryCreatePage')->middleware('auth');
Route::post('/dashboard/productcategories/create', [ProductCategoryController::class, 'store'])->name('productCategoryStore')->middleware('auth');
Route::get('/dashboard/productcategories/edit/{id}', [ProductCategoryController::class, 'edit'])
	->name('productCategoryEditPage')
	->middleware('auth');
Route::put('/dashboard/productcategories/edit/{id}', [ProductCategoryController::class, 'update'])
	->name('productCategoryUpdate')
	->middleware('auth');
Route::delete('/dashboard/productcategories/delete/{id}', [ProductCategoryController::class, 'destroy'])
	->name('productCategoryDelete')
	->middleware('auth');

// Shop
Route::get('/winkel', [ShopController::class, 'index'])->name('shop');
Route::get('/winkel/product/{slug}', [ShopController::class, 'show'])->name('productShow');

// Cart
Route::get('/winkel/cart', [CartController::class, 'index'])->name('cartPage');
Route::post('/winkel/cart', [CartController::class, 'addToCart'])->name('addToCart');
Route::post('/winkel/cart/update', [CartController::class, 'updateCart'])->name('updateCart');
Route::post('/winkel/cart/remove', [CartController::class, 'removeCart'])->name('removeCart');
Route::delete('/winkel/cart/delete', [CartController::class, 'deleteItemFromCart'])->name('deleteItemFromCart');

// Checkout
Route::get('/winkel/checkout', [CheckoutController::class, 'create'])->name('checkoutPage');
Route::post('/winkel/checkout', [CheckoutController::class, 'store'])->name('storeCheckout');

// Orders
Route::get('/dashboard/orders', [OrderController::class, 'index'])->name('orderIndex')->middleware('auth');
Route::get('/dashboard/orders/{id}', [OrderController::class, 'show'])->name('orderShow')->middleware('auth');

// My Orders
Route::get('/dashboard/my-orders', [OrderController::class, 'showMyOrders'])->name('showMyOrders')->middleware('auth');
Route::get('/dashboard/my-orders/{id}', [OrderController::class, 'showMyOrder'])->name('showMyOrder')->middleware('auth');
Route::get('/dashboard/create', [OrderController::class, 'create'])->name('orderCreatePage');

// Customers
Route::get('/dashboard/customers', [CustomerController::class, 'index'])->name('customerIndex')->middleware('auth');
Route::get('/dashboard/customers/{id}', [CustomerController::class, 'show'])->name('customerShow')->middleware('auth');

// Users
Route::get('/dashboard/users', [UserController::class, 'index'])->name('userIndex')->middleware('auth');
Route::get('/dashboard/users/{id}', [UserController::class, 'show'])->name('userShow')->middleware('auth');

// Mollie payments
Route::get('/payment/success/', [CheckoutController::class, 'paymentSuccess'])->name('payment.success');
Route::post('/webhooks/mollie', [CheckoutController::class, 'payment
Webhook'])->name('webhooks.mollie');
