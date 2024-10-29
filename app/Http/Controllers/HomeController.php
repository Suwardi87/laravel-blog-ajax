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

        if ($user->role === 'owner') {
            $totalArticles = Article::count();
            $pendingArticles = Article::where('is_confirm', '0')->count();
            $totalCategories = Category::count();
            $confirmedArticles = Article::where('is_confirm', '1')->count();
            $progressPercentage = $totalArticles > 0 ? ($pendingArticles / $totalArticles) * 100 : 0;
            $categories = Category::withCount('articles')->get(); // Include article counts per category
        } else if ($user->role === 'writer') {
            $totalArticles = Article::where('user_id', $user->id)->count();
            $pendingArticles = Article::where('user_id', $user->id)->where('status', 'pending')->count();
            $categories = Category::whereHas('articles', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->withCount(['articles' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])->get();
        }
        return view('home', compact('totalArticles', 'pendingArticles', 'categories','confirmedArticles','progressPercentage','totalCategories'));
    }
}
