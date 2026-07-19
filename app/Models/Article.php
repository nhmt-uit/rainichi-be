<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Base
{
    use \Astrotomic\Translatable\Translatable;

    protected $table = 'articles';

    protected $fillable = ['image', 'type', 'view', 'is_active', 'sort', 'expired_at', 'created_by', 'updated_by', 'category_id'];

//    mapping translate
    public $translatedAttributes = ['name', 'short_content', 'content', 'seo_name', 'seo_keywords', 'seo_content', 'slug', 'position', 'work_at', 'offer'];


    /**
     * @return relationship user apply
     *
     * */
    public function userApply()
    {
        return $this->hasMany('App\Models\UserApply');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');

    }

    public function isUserApply($user_id)
    {
        $user_apply = $this->userApply()->where('user_id', $user_id)->first();
        return empty($user_apply) ? false : true;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */

    public function applies()
    {
        return $this->hasMany(UserApply::class, 'article_id', 'id');
    }

    public function scopeCategoryId($q, $type)
    {
        if (isset($type)) {
            return $q->where('category_id', (int)$type);
        }
    }

    /**
     * @param $q
     * @param $user_id
     * @return mixed
     */
    public function scopeSearchByCreatedBy($q, $user_id)
    {
        if (isset($user_id)) {
            return $q->where('created_by', (int)$user_id);
        }
    }

    public function scopeOrderByName($q, $column, $order_by_type, $lang)
    {
        if (isset($column) && isset($order_by_type) && isset($lang) && $column === 'name') {
            return $q->join('article_translations as t', function ($join) use ($lang) {
                $join->on('articles.id', '=', 't.article_id')
                    ->where('t.locale', '=', $lang);
            })->groupBy('articles.id')
                ->orderBy('t.name', $order_by_type)->select('articles.*');
        }
    }

    /**
     * @param $q
     * @param $search_string
     * @param $lang
     * @return mixed
     */
    public function scopeSearchBy($q, $search_string, $lang)
    {
        if (isset($search_string)) {
            return $q->orWhereHas('translations',
                function ($query) use ($search_string, $lang) {
                    $query->where('name', 'like', '%' . $search_string . '%');
                    $query->where('locale', $lang);
                }
            );
        }
    }

    /**
     * @param $q
     * @param $search_string
     * @return mixed
     */
    public function scopeGetBySlug($q, $search_string)
    {
        if (isset($search_string)) {
            return $q->orWhereHas('translations',
                function ($query) use ($search_string) {
                    $query->where('slug', '=', $search_string);
                }
            );
        }
    }

    /**
     * @param $value
     * @return void value $expired_at
     */
    public function setExpiredAtAttribute($value)
    {
        $this->attributes['expired_at'] = date('Y-m-d', strtotime($value));
    }

    /**
     * Get next article
     * @return  article
     */

    public function next()
    {
        if ($this->category) {
            return Article::join('category', 'articles.category_id', 'category.id')
                ->where('articles.id', '>', $this->id)
                ->where('category.id', $this->category_id)
                ->orderBy('articles.id', 'ASC')->select('articles.*')->first();
        }

    }

    /**
     * Get previous article
     * @return article
     */
    public function previous()
    {
        if ($this->category) {
            return Article::join('category', 'articles.category_id', 'category.id')
                ->where('articles.id', '<', $this->id)
                ->where('category.id', $this->category_id)
                ->orderBy('articles.id', 'DESC')->select('articles.*')->first();
        }
    }


}
