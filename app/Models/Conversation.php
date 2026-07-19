<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    const AUDIO = 1;
    const VIDEO = 2;
    use \Astrotomic\Translatable\Translatable;
    protected $table = 'conversation';
    public $timestamps = true;
    public $translatedAttributes = ['name', 'sub_title', 'sub_title_web'];
    protected $fillable = ['image', 'audio', 'level_id', 'video', 'is_active', 'type', 'created_by', 'updated_by'];

    public $casts = ['is_active' => 'boolean', 'type' => 'integer', 'level_id' => 'integer'];

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
     * @param $column
     * @param $order_by_type
     * @return mixed
     */
    public function scopeOrderByCustom($q, $column, $order_by_type)
    {
        if (isset($column, $order_by_type)) {
            if ($column !== 'name') {
                return $q->orderBy($column, $order_by_type);
            }
        } else {
            return $q->orderByDesc('id');
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
                return $q->join('conversation_translations as t', function ($join) use ($lang) {
                    $join->on('conversation.id', '=', 't.conversation_id')
                        ->where('t.locale', '=', $lang);
                })->groupBy('conversation.id')
                    ->orderBy('t.name', $order_by_type)->select('conversation.*');
            }
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

    public function groupConversation()
    {
        return $this->belongsToMany('App\Models\GroupChapter', 'conversation_group', 'conversation_id', 'group_chapter_id')->withTimestamps();
    }
}
