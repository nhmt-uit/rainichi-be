<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vocabulary extends Model
{
    use \Astrotomic\Translatable\Translatable;
    protected $table = 'vocabulary';
    public $translatedAttributes = ['chinese_vietnamese_word', 'meaning', 'example1', 'example2', 'audio_example_1', 'audio_example_2'];
    protected $fillable = [
        'vocabulary',
        'image',
        'audio',
        'hiragana',
        'kanji',
        'katakana',
        'spelling',
        'is_active',
        'svg',
        'type',
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
    public function scopeVocabularyActive($q, $status)
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
            return $q->where('vocabulary', 'like', '%' . $search_string . '%')->orWhereHas(
                'translations',
                function ($query) use ($search_string, $lang) {
                    $query->where('meaning', 'like', '%' . $search_string . '%');
                    $query->orWhere('chinese_vietnamese_word', 'like', '%' . $search_string . '%');
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

            if ($column !== 'name' && $column !== 'course_type_name') {
                return $q->orderBy($column, $order_by_type);
            }
        } else {
            return $q->orderBy('updated_at', 'desc');
        }
    }

    public function groupVocabulary()
    {
        return $this->belongsToMany('App\Models\GroupChapter', 'vocabulary_group', 'vocabulary_id', 'group_chapter_id')->withTimestamps();
    }
}
