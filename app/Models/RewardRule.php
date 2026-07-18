<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RewardRule extends Model
{
    public $table = 'reward_rules';

    use \Dimsav\Translatable\Translatable;

    public $timestamps = true;

    public $fillable = ['credit'
        , 'route'
        , 'is_active'
        , 'start_date'
        , 'end_date'
    ];

    public $translatedAttributes = ['description', 'method'];

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
    public function scopeSearchBy($q, $search_string)
    {
        if (isset($search_string)) {
            return $q->where('description', 'like', '%' . $search_string . '%')->orWhere('credit', 'like', '%' . $search_string . '%')->orWhere('method', 'like', '%' . $search_string . '%');
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
