<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductCategoryController;

// Pages
Route::get('/', function () { return view('home'); })->name('home');
Route::get('/risale-i-nur', function () { return view('risale'); })->name('risale');
Route::get('/said-nursi', function () { return view('saidnursi'); })->name('saidnursi');
Route::get('/winkel', function () { return view('shop'); })->name('shop');
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