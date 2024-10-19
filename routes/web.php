<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\CategoryController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->group(function () {
    Route::get('dashboard', function () {
        return view('home');
    })->name('admin.dashboard');

    Route::get('categories/serverside', [CategoryController::class, 'serverside'])->name('admin.categories.serverside');

    Route::resource('categories', CategoryController::class)
    // ->except('edit','create')
    ->names('admin.categories');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
