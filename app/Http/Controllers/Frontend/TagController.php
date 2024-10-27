<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Frontend\TagService;
use App\Http\Controllers\Services\Frontend\ArticleService;
use App\Http\Controllers\Services\Frontend\CategoryService;

class TagController extends Controller
{
    public function __construct(
        private TagService $tagService,
        private CategoryService $categoryService,
        private ArticleService $articleService
    ){}

    public function index()
    {
        $articles = $this->articleService->all();

        return view('frontend.article.index', [
            'articles' => $articles,
            'categories' => $this->categoryService->all(),
            'popular_articles' => $this->articleService->popularArticles(),
            'tags' => $this->tagService->all(),
        ]);
    }

    public function showByTag(string $slug)
    {
        $tag = $this->tagService->getFirstBy('slug', $slug);

        if ($tag == null) {
            return view('frontend.custom-error.404', [
                'url' => url('/tag/' . $slug),
            ]);
        }

        $articles = $this->articleService->showByTag($slug);

        return view('frontend.tag.show', [
            'tag' => $tag,
            'articles' => $articles,
        ]);
    }

    public function show(string $slug)
    {
        $tag = $this->tagService->getFirstBy('slug', $slug);

        if ($tag == null) {
            return view('frontend.custom-error.404', [
                'url' => url('/tag/' . $slug),
            ]);
        }

        $articles = $this->articleService->showByTag($slug);

        return view('frontend.tag.show', [
            'tag' => $tag,
            'articles' => $articles,
            'popular_articles' => $this->articleService->popularArticles(),
            'tags' => $this->tagService->all(),
            'categories' => $this->categoryService->all(),
        ]);
    }

}
