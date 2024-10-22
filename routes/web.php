<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\TagController;
use App\Http\Controllers\Backend\ArticleController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\WriterController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('dashboard', function () {
        return view('home');
    })->name('admin.dashboard');

    // Articels
    Route::get('articles/serverside', [ArticleController::class, 'serverside'])->name('admin.articles.serverside');
    Route::resource('articles', ArticleController::class)
    ->names('admin.articles');

    //Category
    Route::post('categories/import', [CategoryController::class, 'import'])->name('admin.categories.import');

    Route::get('categories/serverside', [CategoryController::class, 'serverside'])->name('admin.categories.serverside');
    Route::resource('categories', CategoryController::class)
    ->except('edit','create')
    ->names('admin.categories');

    //Tag
    Route::get('tags/serverside', [TagController::class, 'serverside'])->name('admin.tags.serverside');
    Route::resource('tags', TagController::class)
    ->except('edit','create')
    ->names('admin.tags');

     // writers
     Route::get('writers/serverside', [WriterController::class, 'serverside'])->name('admin.writers.serverside');
     Route::resource('writers', WriterController::class)
     ->only('index')
     ->names('admin.writers');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
