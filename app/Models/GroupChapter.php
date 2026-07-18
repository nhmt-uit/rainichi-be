<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupChapter extends Model
{
    use \Dimsav\Translatable\Translatable;

    public $timestamps = true;

    protected $table = 'group_chapter';

    public $translatedAttributes = ['name'];

    protected $fillable = [
        'level_id',
        'chapter_id',
        'created_by',
        'updated_by',
        'is_active'
    ];
    public $casts = ['is_active' => 'boolean'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

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
    public function chapter()
    {
        return $this->belongsTo('App\Models\Chapter', 'chapter_id', 'id');
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
     * @param $search_string
     * @param $lang
     * @return mixed
     */
    public function scopeSearchByString($q, $search_string, $lang)
    {
        if (isset($search_string) && isset($lang)) {
            return $q->whereHas(
                'translations',
                function ($query) use ($search_string, $lang) {
                    $query->where('name', 'like', '%' . $search_string . '%');
                    $query->where('locale', $lang);
                }
            );
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
     * @param $q
     * @param $level
     * @return mixed
     */
    public function scopeSearchByChapter($q, $level)
    {
        if (isset($level)) {
            return $q->where('chapter_id', (int)$level);
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

            if ($column !== 'name' && $column !== 'course_type_name') {
                return $q->orderBy($column, $order_by_type);
            }
        } else {
            return $q->orderBy('created_at', 'desc');
        }
    }

    /**
     * @param $q
     * @param $column
     * @param $order_by_type
     * @param $lang
     * @return mixed
     */
    public function scopeOrderByName($q, $column, $order_by_type, $lang)
    {

        if (isset($column) && isset($order_by_type) && isset($lang)) {
            if ($column === 'name') {
                return $q->join('group_chapter_translations as t', function ($join) use ($lang) {
                    $join->on('group_chapter.id', '=', 't.group_chapter_id')
                        ->where('t.locale', '=', $lang);
                })->groupBy('group_chapter.id')
                    ->orderBy('t.name', $order_by_type)->select('group_chapter.*');
            }
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function lessonChapterVocabulary()
    {
        return $this->belongsToMany('App\Models\Lesson', 'lesson_vocabulary', 'lesson_id', 'group_chapter_id')->withTimestamps();
    }

    /**
     * @param $q
     * @param $group_id
     * @param $has_lesson
     * @return mixed
     */
    public function scopeGetByLesson($q, $group_id, $has_lesson)
    {
        if (isset($group_id) && $has_lesson)
            return $q->whereIn('id', $group_id);
    }

    public function scopeSearchByType($q, $type)
    {
        if (isset($type)) {
            return $q->whereHas(
                'chapter',
                function ($query) use ($type) {
                    $query->where('type', $type);
                }
            );
        }
    }

}
