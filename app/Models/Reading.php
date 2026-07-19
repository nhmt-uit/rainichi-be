<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reading extends Model
{
    use \Astrotomic\Translatable\Translatable;
    protected $table = 'reading';
    public $translatedAttributes = ['name', 'description'];
    protected $fillable = [
        'paragraph',
        'is_active',
        'level_id',
        'created_by',
        'updated_by'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function levels()
    {
        return $this->belongsTo('App\Models\Level', 'level_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groupReading()
    {
        return $this->belongsToMany('App\Models\GroupChapter', 'reading_group', 'reading_id', 'group_chapter_id')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function questions()
    {
        return $this->belongsToMany('App\Models\Question', 'reading_questions');
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

    /**
     * @param $q
     * @param $level
     * @return mixed
     */
    public function scopeSearchByLevel($q, $level)
    {
        if (isset($level)) {
            return $q->whereIn('level_id', json_decode($level));
        }
    }

    /**
     * Get list vocabulary bu active status
     * @param $q
     * @param $status
     * @return mixed
     */
    public function scopeReadingActive($q, $status)
    {
        if (isset($status)) {
            if ((boolean)$status === true || (boolean)$status === false)
                return $q->where('is_active', (boolean)$status);
        }


    }

    /**
     * @param $q
     * @param $search_string
     * @return mixed
     */
    public function scopeSearchBy($q, $search_string, $lang)
    {
        if (isset($search_string)) {
            return $q->where('paragraph', 'like', '%' . $search_string . '%')->orWhereHas(
                'translations',
                function ($query) use ($search_string, $lang) {
                    $query->where('name', 'like', '%' . $search_string . '%');
                    $query->orWhere('description', 'like', '%' . $search_string . '%');
                    $query->where('locale', $lang);
                }
            );
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
        if (isset($column, $order_by_type)) {
            return $q->orderBy($column, $order_by_type);

        } else {
            return $q->orderBy('created_at', 'desc');
        }
    }
}
