<?php

use App\Http\Controllers\DiscountCodeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MyOrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\PickupLocationController;


// Both admin and user can access
Route::middleware(['auth', 'role:admin,user'])->group(function () {

	// Dashboard
	Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

	// Profile
	Route::get('/dashboard/profile', [ProfileController::class, 'editPage'])->name('editProfile');
	Route::post('/dashboard/profile', [ProfileController::class, 'updateProfile'])->name('updateProfile');

	// My Orders
	Route::get('/dashboard/my-orders', [MyOrderController::class, 'showMyOrders'])->name('showMyOrders');
	Route::get('/dashboard/my-orders/{id}', [MyOrderController::class, 'showMyOrder'])->name('showMyOrder');	
	Route::get('/dashboard/my-orders/{id}/invoice', [MyOrderController::class, 'download_invoice'])->name('my_orders.invoice');
   
});


// Only admin can access
Route::middleware(['auth', 'role:admin'])->group(function () {

	// Products
	Route::get('/dashboard/products', [ProductController::class, 'index'])->name('productIndex');
	Route::get('/dashboard/products/create', [ProductController::class, 'create'])->name('productCreatePage');
	Route::post('/dashboard/products/create', [ProductController::class, 'store'])->name('productStore');
	Route::get('/dashboard/products/edit/{id}', [ProductController::class, 'edit'])->name('productEditPage');
	Route::put('/dashboard/products/edit/{id}', [ProductController::class, 'update'])->name('productUpdate');
	Route::delete('/dashboard/products/delete/{id}', [ProductController::class, 'destroy'])->name('productDelete');
	Route::get('/dashboard/products/delete/{id}', [ProductController::class, 'get'])->name('productDelete_get');

	// Productcategories
	Route::get('/dashboard/productcategories', [ProductCategoryController::class, 'index'])->name('productCategoryIndex');
	Route::get('/dashboard/productcategories/create', [ProductCategoryController::class, 'create'])->name('productCategoryCreatePage');
	Route::post('/dashboard/productcategories/create', [ProductCategoryController::class, 'store'])->name('productCategoryStore');
	Route::get('/dashboard/productcategories/edit/{id}', [ProductCategoryController::class, 'edit'])->name('productCategoryEditPage');
	Route::post('/dashboard/productcategories/edit/{id}', [ProductCategoryController::class, 'update'])->name('productCategoryUpdate');
	Route::delete('/dashboard/productcategories/delete/{id}', [ProductCategoryController::class, 'destroy'])->name('productCategoryDelete');
	Route::get('/dashboard/productcategories/delete/{id}', [ProductCategoryController::class, 'get'])->name('productCategoryDeleteGet');

	// Orders
	Route::get('/dashboard/orders/create', [OrderController::class, 'create'])->name('orderCreatePage');
	Route::get('/dashboard/orders', [OrderController::class, 'index'])->name('orderIndex');
	Route::get('/dashboard/orders/{id}', [OrderController::class, 'show'])->name('orderShow');	
	Route::post('/dashboard/orders/create', [OrderController::class, 'store'])->name('orderStore');
	Route::put('/dashboard/orders/{id}', [OrderController::class, 'update'])->name('orderUpdate');	
	Route::post('/dashboard/orders/generate-invoice/{id}', [OrderController::class, 'generateInvoice'])->name('generateInvoice');
	Route::post('/dashboard/orders/send-email/{id}', [OrderController::class, 'sendOrderEmailWithInvoice'])->name('sendOrderEmailWithInvoice');
	Route::get('/dashboard/orders//invoice/{id}', [OrderController::class, 'download_invoice'])->name('orders.invoice');

	// Customers
	Route::get('/dashboard/customers', [CustomerController::class, 'index'])->name('customerIndex');
	Route::get('/dashboard/customers/{id}', [CustomerController::class, 'show'])->name('customerShow');

	// Users
	Route::get('/dashboard/users', [UserController::class, 'index'])->name('userIndex');
	Route::get('/dashboard/users/create', [UserController::class, 'create'])->name('userCreate');
	Route::post('/dashboard/users/create', [UserController::class, 'store'])->name('userStore');
	Route::get('/dashboard/users/{id}', [UserController::class, 'show'])->name('userShow');
	Route::put('/dashboard/users/{id}', [UserController::class, 'update'])->name('userEdit');

  //Discount codes
  Route::get('/dashboard/discount-codes', [DiscountCodeController::class, 'index'])->name('discountIndex');
  Route::get('/dashboard/discount-codes/create', [DiscountCodeController::class, 'create'])->name('discountCreate');
  Route::post('/dashboard/discount-codes/create', [DiscountCodeController::class, 'store'])->name('discountStore');
  Route::get('/dashboard/discount-codes/edit/{id}', [DiscountCodeController::class, 'edit'])->name('discountEdit');
  Route::put('/dashboard/discount-codes/edit/{id}', [DiscountCodeController::class, 'update'])->name('discountUpdate');
  Route::delete('/dashboard/discount-codes/delete/{id}', [DiscountCodeController::class, 'destroy'])->name('discountDelete');
  Route::get('/dashboard/discount-codes/delete/{id}', [DiscountCodeController::class, 'get'])->name('discountDelete_get');

});


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
Route::get('/winkel/checkout/success', [CheckoutController::class, 'checkoutSuccess'])->name('checkoutSuccessPage');

// Auth pages
Route::get('/login', [AuthController::class, 'loginPage'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'loginUser'])->name('loginUser')->middleware('guest');
Route::get('/logout', [AuthController::class, 'logoutGet'])->name('logout_get')->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Forgot password
Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request')->middleware('guest');
Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink'])
	->name('password.email')
	->middleware(['guest', 'throttle:3,10']); // max 3 requests per 10 minutes
Route::get('/reset-password/{token}', [AuthController::class, 'resetPassword'])->name('password.reset')->middleware('guest');
Route::get('/reset-password', [AuthController::class, 'get'])->name('resetNoToken')->middleware('guest');
Route::post('/reset-password', [AuthController::class, 'resetPasswordHandler'])->name('password.update')->middleware('guest');

// Pages
Route::get('/', function () { return view('home'); })->name('home');
Route::get('/risale-i-nur', function () { return view('risale'); })->name('risale');
Route::get('/said-nursi', function () { return view('saidnursi'); })->name('saidnursi');
Route::get('/contact', function () { return view('contact'); })->name('contact');


// Mollie payments
Route::get('/payment/success/', [CheckoutController::class, 'paymentSuccess'])->name('payment.success');
Route::post('/webhooks/mollie', [CheckoutController::class, 'payment
Webhook'])->name('webhooks.mollie');

// Admin/custom pickup locations API (used by admin order page custom widget)
Route::get('/pickup-locations', [PickupLocationController::class, 'index'])->name('pickup.locations');

