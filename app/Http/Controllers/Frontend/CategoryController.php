<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Frontend\TagService;
use App\Http\Controllers\Services\Frontend\ArticleService;
use App\Http\Controllers\Services\Frontend\CategoryService;

class CategoryController extends Controller
{
    public function __construct(
        private CategoryService $categoryService,
        private ArticleService $articleService,
        private TagService $tagService)
    {}

    public function index()
    {
     return view('frontend.category.index', [
            'categories' => $this->categoryService->all(),
            'popular_articles' => $this->articleService->popularArticles(),
            'tags' => $this->tagService->all(),
        ]);
    }

    public function show(string $slug)
    {
        $category = $this->categoryService->getFirstBy('slug', $slug);

        if ($category == null) {
            return view('frontend.custom-error.404', [
                'url' => url('/category/' . $slug),
            ]);
        }

        $articles = $this->articleService->showByCategory($slug);

        return view('frontend.category.show', [
            'category' => $category,
            'popular_articles' => $this->articleService->popularArticles(),
            'tags' => $this->tagService->all(),
            'categories' => $this->categoryService->all(),
            'articles' => $articles,
        ]);
    }
}
