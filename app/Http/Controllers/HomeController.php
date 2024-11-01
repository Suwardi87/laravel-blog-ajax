<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();
        // Initialize statistics
        $totalArticles = 0;
        $pendingArticles = 0;
        $totalCategories = Category::count();
        $confirmedArticles = 0;
        $progressPercentage = 0;
        $categories = [];
        if ($user->hasRole('owner')) {
            // Owner statistics
            $totalArticles = Article::count();
            $pendingArticles = Article::where('is_confirm',  '0')->count();
            $confirmedArticles = Article::where('is_confirm', '1')->count();
            $progressPercentage = $totalArticles > 0 ? ($confirmedArticles / $totalArticles) * 100 : 0;
            $categories = Category::withCount('articles')->get(); // Include article counts per category
        } else if ($user->hasRole('writer')) {
            // Writer statistics
            $totalArticles = Article::where('user_id', $user->id)->count();
            $pendingArticles = Article::where('user_id', $user->id)->where('published', '0')->count();
            $confirmedArticles = Article::where('user_id', $user->id)->where('published', '1')->count();
            $progressPercentage = $totalArticles > 0 ? ($confirmedArticles / $totalArticles) * 100 : 0;
            $categories = Category::whereHas('articles', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->withCount(['articles' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])->get();
        }

        return view('home', [
            'totalArticles' => $totalArticles,
            'pendingArticles' => $pendingArticles,
            'totalCategories' => $totalCategories,
            'confirmedArticles' => $confirmedArticles,
            'progressPercentage' => $progressPercentage,
            'categories' => $categories
        ]);
    }
}
