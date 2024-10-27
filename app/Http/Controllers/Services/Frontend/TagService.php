<?php

namespace App\Http\Controllers\Services\Frontend;

use App\Models\Tag;

class TagService
{
    // get random tag
    public function randomTag()
    {
        return Tag::inRandomOrder()->take(6)->get(['id', 'name', 'slug']);
    }

    // // get tag by slug
    public function getFirstBy(string $column, string $value)
    {
        return Tag::where($column, $value)->first();
    }

    public function all()
    {
        return Tag::inRandomOrder()->take(6)->get(['id', 'name', 'slug']);
    }
}
