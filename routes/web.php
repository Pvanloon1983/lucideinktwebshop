<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Pages
Route::get('/', function () { return view('home'); })->name('home');
Route::get('/risale-i-nur', function () { return view('risale'); })->name('risale');
Route::get('/said-nursi', function () { return view('saidnursi'); })->name('saidnursi');
Route::get('/winkel', function () { return view('shop'); })->name('shop');
Route::get('/contact', function () { return view('contact'); })->name('contact');

// Dashboard
Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard')->middleware('auth');

// Auth pages
Route::get('/register', [AuthController::class, 'registerPage'])->name('register');
Route::post('/register', [AuthController::class, 'registerUser'])->name('registerUser');
Route::get('/login', [AuthController::class, 'loginPage'])->name('login');
Route::post('/login', [AuthController::class, 'loginUser'])->name('loginUser');
Route::get('/logout', [AuthController::class, 'logoutGet'])->name('logout_get');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');