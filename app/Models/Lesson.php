<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    use \Astrotomic\Translatable\Translatable;

    public $timestamps = true;

    protected $table = 'lesson';

    public $translatedAttributes = ['name', 'slug', 'description'];

    protected $fillable = ['level_id', 'avatar', 'is_active', 'type', 'created_by', 'updated_by'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level()
    {
        return $this->belongsTo('App\Models\Level');
    }

    public function submit()
    {
        return $this->hasMany('App\Models\ExerciseSubmit', 'lesson_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function chapters()
    {
        return $this->belongsToMany('App\Models\Chapter', 'lesson_chapter', 'lesson_id', 'chapter_id')->orderBy('id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function courses()
    {
        return $this->belongsToMany('App\Models\Course', 'course_lesson', 'lesson_id', 'course_id')
            ->withPivot('has_trial', 'sort_order', 'is_active')->orderBy('course_lesson.sort_order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    /**
     * @param $q
     * @param $level_id
     * @return mixed
     */
    public function scopeGetByLevel($q, $level_id)
    {
        if (isset($level_id)) {
            return $q->whereIn('level_id', json_decode($level_id));
        }
    }

    /**
     * @param $q
     * @param $created_by
     * @return mixed
     */
    public function scopeGetByUser($q, $created_by)
    {
        if (isset($created_by)) {
            return $q->where('created_by', (int)$created_by);
        }
    }

    /**
     * @param $q
     * @param $search_string
     * @param $lang
     * @return mixed
     */
    public function scopeSearchString($q, $search_string, $lang)
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

    public function scopeChapter()
    {

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
                return $q->join('lesson_translations as t', function ($join) use ($lang) {
                    $join->on('lesson.id', '=', 't.lesson_id')
                        ->where('t.locale', '=', $lang);
                })->groupBy('lesson.id')
                    ->orderBy('t.name', $order_by_type)->select('lesson.*');
            }
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
            return $q->orderByDesc('id');
        }
    }

    /**
     * @param $q
     * @param $lesson
     * @param $course_id
     * @return mixed
     */
    public function scopeGetByCourseId($q, $lesson, $course_id)
    {
        if (isset($lesson)) {
            return $q->whereIn('lesson.id', $lesson)->join('course_lesson as c', 'c.lesson_id', '=', 'lesson.id')
                ->where('c.course_id', $course_id)->select('c.sort_order', 'c.course_id', 'c.has_trial', 'c.is_active', 'lesson.*');
        }
    }

    public function scopeGetByChapterId($q, $chapter_id)
    {
        if (isset($chapter_id)) {
            return $q->join('lesson_chapter as c', 'c.lesson_id', '=', 'lesson.id')
                ->where('c.chapter_id', $chapter_id)->select('lesson.*');
        }
    }
}
