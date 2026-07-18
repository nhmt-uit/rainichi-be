<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserApply extends Base
{
    protected $table = 'user_apply';
    protected $fillable = ['user_id', 'article_id', 'is_view', 'is_active',
        'name', 'email', 'info', 'cv', 'ip_address', 'user_agent', 'cv_name', 'updated_by'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }

    /**
     * @param $q
     * @param $user_id
     * @return mixed
     */
    public function scopeSearchByUpdatedBy($q, $user_id)
    {
        if (isset($user_id)) {
            return $q->where('created_by', (int)$user_id);
        }
    }

    /**
     * @param $q
     * @param $column
     * @param $order_by_type
     * @return mixed
     */
    public function scopeOrderByCustom($q, $column, $order_by_type)
    {
        return $q->orderBy($column, $order_by_type);
    }

    /**
     * @param $q
     * @param $column
     * @param $search_string
     * @return mixed
     */
    public function scopeSearchByColumn($q, $column, $search_string)
    {
        return $q->where($column, 'like', '%' . $search_string . '%');
    }

    /**
     * @param $q
     * @param $is_view
     * @return mixed
     */
    public function scopeIsView($q, $is_view)
    {
        if (isset($is_view)) {
            return $q->where('is_view', $is_view);
        }
    }

    /**
     * @param $q
     * @param $article_id
     */
    public function scopeGetArticle($q, $article_id)
    {
        if(isset($article_id)){
            $q->where('article_id', $article_id);
        }
    }
}
