<?php


namespace App\Http\Controllers;

use App\Models\Article;

class PostController extends Controller
{
    public function detail($slug, $type)
    {
        $article = Article::query()->getBySlug($slug)->first();
        if ($article) {
            return view('post-detail', ['post' => $article, 'type' => $type]);
        }
        else {
            return view('welcome');
        }
    }
}
