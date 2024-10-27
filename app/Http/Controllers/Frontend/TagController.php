<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Tag;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Services\Frontend\TagService;
use App\Http\Controllers\Services\Frontend\ArticleService;

class TagController extends Controller
{
    public function __construct(
        private TagService $tagService,
        private ArticleService $articleService
    ){}

    public function show(string $slug)
    {
        // eloquent
        $tag = Tag::where('slug', $slug)->first();
        if (!$tag) {
            return view('frontend.custom-error.404', [
                'url' => url('/tag/' . $slug),
            ]);
        }

        // // add view
        $tag->increment('views');

         // get category
        //  $categories = $this->tagService->randomCategory();

         return view('frontend.tag.show', [
             'tag' => $tag,
            //  'categories' => $categories,
            //  'popular_tags' => $this->tagService->populartags(),
             'tags' => $this->tagService->all(),
            //  'related_tags' => $this->tagService->relatedtags($article->slug),
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
}
