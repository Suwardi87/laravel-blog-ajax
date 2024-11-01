<?php

namespace App\Http\Controllers\Services\Backend;

use App\Models\Tag;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Mail;

class ArticleService
{
    public function dataTable($request)
    {
        if ($request->ajax()) {
            $user = auth()->user();
            $limit = $request->length;
            $start = $request->start;
    
            // Determine the query based on user role
            $query = Article::with('category:id,name', 'tags:id,name')->withTrashed();
    
            if ($user->hasRole('owner')) {
                $totalData = $query->count();
                $data = $query->latest()->offset($start)->limit($limit);
    
                // Filter if search value is present
                if (!empty($request->search['value'])) {
                    $data = $data->filter($request->search['value']);
                }
            } else {
                $totalData = Article::where('user_id', $user->id)->count();
                $data = $query->where('user_id', $user->id)->latest()->offset($start)->limit($limit);
    
                // Filter if search value is present
                if (!empty($request->search['value'])) {
                    $data = $data->filter($request->search['value']);
                }
            }
    
            // Get the filtered data
            $data = $data->get(['id', 'uuid', 'title', 'category_id', 'views', 'published','is_confirm', 'deleted_at']);
    
            // Calculate totalFiltered for non-empty search
            $totalFiltered = empty($request->search['value']) ? $totalData : $data->count();
    
            return DataTables::of($data)
                ->addIndexColumn()
                ->setOffset($start)
                ->editColumn('title', function ($data) {
                    return $data->deleted_at 
                        ? '<span class="text-danger">' . $data->title . '</span>' 
                        : $data->title;
                })
                ->editColumn('category_id', function ($data) {
                    return '<span class="badge bg-secondary">' . $data->category->name . '</span>';
                })
                ->editColumn('published', function ($data) use ($user) {
                    if ($user->hasRole('owner')) {
                        return $data->published == 0 
                            ? '<button type="button" class="btn btn-sm btn-danger" onclick="publishedModal(this)" data-uuid="' . $data->uuid . '">Draft</button>' 
                            : '<span class="badge bg-success">Published</span>';
                    }
                    return $data->published == 0 
                        ? '<span class="badge bg-danger">Draft</span>' 
                        : '<span class="badge bg-success">Published</span>';
                })
                ->editColumn('is_confirm', function ($data) use ($user) {
                    if ($user->hasRole('owner')) {
                        return $data->is_confirm == 0 
                            ? '<button type="button" class="btn btn-sm btn-danger" onclick="confirmModal(this)" data-uuid="' . $data->uuid . '">Belum</button>' 
                            : '<span class="badge bg-success">Confirm</span>';
                    }
                    return $data->is_confirm == 0 
                        ? '<span class="badge bg-danger">Belum</span>' 
                        : '<span class="badge bg-success">Confirm</span>';
                })
                
                
                ->editColumn('views', function ($data) {
                    return '<span class="badge bg-secondary">' . $data->views . 'x</span>';
                })
                ->addColumn('tag_id', function ($data) {
                    return $data->tags->map(function ($tag) {
                        return '<span class="badge bg-secondary ms-1">' . $tag->name . '</span>';
                    })->implode('');
                })
                ->addColumn('action', function ($data) {
                    return '
                        <div class="text-center">
                            <div class="btn-group">
                                <a href="' . route('admin.articles.show', $data->uuid) . '" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="' . route('admin.articles.edit', $data->uuid) . '" class="btn btn-sm btn-success">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" onclick="deleteData(this)" data-id="' . $data->uuid . '">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    ';
                })
                ->rawColumns(['title', 'category_id', 'tag_id', 'published', 'is_confirm', 'views', 'action'])
                ->with([
                    'recordsTotal' => $totalData,
                    'recordsFiltered' => $totalFiltered,
                    'start' => $start
                ])
                ->make();
        }
    }


    public function getCategory()
    {
        return Category::latest()->get(['id', 'name']);
    }

    public function getTag()
    {
        return Tag::latest()->get(['id', 'name']);
    }

    public function getFirstBy(string $column, string $value, bool $relation = false)
    {
        if ($relation == true && auth()->user()->hasRole('owner')) {
            return Article::with('user:id,name', 'category:id,name', 'tags:id,name')->where($column, $value)->withTrashed()->firstOrFail();
        } elseif ($relation == false && auth()->user()->hasRole('owner')) {
            return Article::where($column, $value)->withTrashed()->firstOrFail();
        } else {
            return Article::where($column, $value)->firstOrFail();
        }
    }

    public function all()
    {
        $article = Article::with('category:id,name,slug', 'user:id,name')
            ->select(['id', 'title', 'slug', 'category_id', 'user_id', 'published', 'is_confirm', 'views', 'image', 'published_at'])
            ->orderBy('published_at', 'desc')
            ->where('published', true)
            ->where('is_confirm', true)
            ->SimplePaginate(6);

        return $article;
    }

    public function create(array $data)
    {
        $data['slug'] = Str::slug($data['title']);

        if (array_key_exists('published', $data) && $data['published'] == 1) {
            $data['published_at'] = date('Y-m-d');
        }else{
            $data['published_at'] = null;
        }

           
        // insert article_tag
        $article = Article::create($data);
        $article->tags()->sync($data['tag_id'] ?? []);

        return $article;
    }

    public function update(array $data, string $uuid)
    {
        $data['slug'] = Str::slug($data['title']);

        if ($data['published'] == 1) {
            $data['published_at'] = date('Y-m-d');
        }

        // insert article_tag
        $article = Article::where('uuid', $uuid)->firstOrFail();
        $article->update($data);
        $article->tags()->sync($data['tag_id']);

        return $article;
    }

    public function delete(string $uuid)
    {
        $getArticle = $this->getFirstBy('uuid', $uuid);

        // Storage::disk('public')->delete('images/' . $getArticle->image);

        // $getArticle->tags()->detach();
        $getArticle->tags()->updateExistingPivot($getArticle->tags, ['deleted_at' => now()]); // soft delete
        $getArticle->delete(); // soft delete

        return $getArticle;
    }

    public function restore(string $uuid)
    {
        $getArticle = $this->getFirstBy('uuid', $uuid);
        $getArticle->restore();

        return $getArticle;
    }

    public function forceDelete(string $uuid)
    {
        $getArticle = $this->getFirstBy('uuid', $uuid);

        Storage::disk('public')->delete('images/' . $getArticle->image);

        $getArticle->tags()->detach(); // force delete
        $getArticle->forceDelete(); // force delete

        return $getArticle;
    }
}
