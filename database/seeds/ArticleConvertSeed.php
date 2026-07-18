<?php

use Illuminate\Database\Seeder;
use App\Models\ArticleTranslations;

class ArticleConvertSeed extends Seeder
{
    /**
     * Run the database seeds.
     * create: php artisan make:seed ArticleConvertSeed
     * Run: php artisan db:seed --class=ArticleConvertSeed
     * @return void
     */
    public function run()
    {
        $allArticles = ArticleTranslations::query()->get()->all();
        foreach ($allArticles as $key => $article)
        {
            $article->slug = str_slug($article->name, '-');
            $article->save();
        }
    }
}
