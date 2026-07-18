<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleTranslations extends Model
{
    public $timestamps = false;
    protected $table = 'article_translations';
    protected $fillable = ['name', 'short_content', 'content', 'seo_name', 'seo_keywords', 'seo_content', 'offer', 'slug'];
}
