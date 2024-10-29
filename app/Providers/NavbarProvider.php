<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\Services\Frontend\TagService;
use App\Http\Controllers\Services\Frontend\ArticleService;
use App\Http\Controllers\Services\Frontend\CategoryService;

class NavbarProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        View::composer('frontend.layout.partials._navbar', function ($view) {
            $articleService = app(ArticleService::class);
            $view->with('popular_articles', $articleService->popularArticles());

            $articleService = app(ArticleService::class);
            $view->with('articles', $articleService->all());

            $categoryService = app(CategoryService::class);
            $view->with('categories', $categoryService->randomCategory());

            $tagService = app(TagService::class);
            $view->with('tags', $tagService->randomTag());
        });
    }
}
