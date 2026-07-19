<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{

    const SINGLE_CHOICE = 1;
    const MULTIPLE_CHOICE = 2;

    public $table = 'question';

    public $timestamps = true;

    use \Astrotomic\Translatable\Translatable;

    public $translatedAttributes = ['name', 'description'];

    protected $fillable = [
        'question',
        'paragraph',
        'image',
        'media',
        'media_description',
        'level_id',
        'chapter_id',
        'is_active',
        'hidden_in_list',
        'type',
        'is_skill',
        'category',
        'score',
        'parent_id',
        'created_by',
        'updated_by'
    ];
    protected $appends = ['total_question', 'total_score'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function getImageAttribute()
    {
        return array_key_exists('image', $this->attributes) && $this->attributes['image'] != null ? media_url_web( $this->attributes['image']) : null;
    }

    public function getMediaAttribute()
    {
        return array_key_exists('media', $this->attributes) && $this->attributes['media'] != null ? media_url_web( $this->attributes['media']) : null;
    }

    public function getTotalQuestionAttribute()
    {
        return count($this->child_questions) > 0 ? count($this->child_questions) : 1;
    }

    public function getTotalScoreAttribute()
    {
        return count($this->child_questions) > 0 ? $this->child_questions()->sum('score') : $this->score;
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function answer()
    {
        return $this->hasMany('App\Models\Answer', 'question_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function child_questions()
    {
        return $this->hasMany('App\Models\Question', 'parent_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function groupQuestion()
    {
        return $this->belongsToMany('App\Models\QuestionGroup', 'question_group', 'question_id', 'group_chapter_id')->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function readings()
    {
        return $this->belongsToMany('App\Models\Reading', 'reading_questions');
    }

    public function test()
    {
        return $this->belongsToMany('App\Models\Reading', 'reading_questions');
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
     * @param $cat
     * @return mixed
     */
    public function scopeSearchByCategory($q, $cat)
    {
        if (isset($cat)) {
            return $q->where('category', (int)$cat);
        }
    }

    /**
     * @param $q
     * @param $is_listening
     * @return mixed
     */
    public function scopeSearchByListening($q, $is_listening)
    {
        if (isset($is_listening)) {
            return $q->where('is_skill', (boolean)$is_listening);
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
     * @param $search_string
     * @return mixed
     */
    public function scopeSearchBy($q, $search_string)
    {
        if (isset($search_string)) {
            return $q->where('question', 'like', '%' . $search_string . '%');
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

    /**
     * @param $q
     * @param $column
     * @param $order_by_type
     * @param $lang
     * @return mixed
     */
    public function scopeOrderByName($q, $column, $order_by_type, $lang)
    {
        if (isset($column) && isset($order_by_type) && isset($lang) && $column === 'name') {
            return $q->join('question_translations as t', function ($join) use ($lang) {
                $join->on('question.id', '=', 't.question_id')
                    ->where('t.locale', '=', $lang);
            })->groupBy('question.id')
                ->orderBy('t.name', $order_by_type)->select('question.*');
        }
    }

    /**
     * @param int $test_time
     * @return mixed
     */
    public static function getQuestionsByTestTime($test_time){
        return self::query()->with('child_questions.answer')
            ->join('question_group', 'question.id', '=', 'question_group.question_id')
            ->join('test_questions', 'test_questions.group_chapter_id', '=', 'question_group.group_chapter_id')
            ->where('test_questions.test_time_id', $test_time)
            ->whereNotNull('test_questions.test_chapter_id')->select('question.*')->get();
    }
}
