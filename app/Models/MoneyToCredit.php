<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MoneyToCredit extends Model
{
    public $table = 'convert_money_to_credit';

    public $fillable = [
        'credit'
        , 'price'
        , 'discount'
        , 'is_active'
        , 'image',
        'created_by',
        'package_id'

    ];

    public $timestamps = true;

    protected $casts = ['is_active' => 'boolean'];

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
    public function scopeSearchBy($q, $search_string)
    {
        if (isset($search_string)) {
            return $q->where('credit', 'like', '%' . $search_string . '%')->orWhere('price', 'like', '%' . $search_string . '%')->orWhere('discount', 'like', '%' . $search_string . '%');
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
