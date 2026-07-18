<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    const PENDING = 0;
    const REJECT = 1;
    const DONE = 2;

    //Declare customer_type_id
    const CUSTOMER_PERSONAL_AND_ENTERPRISE = 1;
    const CUSTOMER_PERSONAL = 2;
    const  CUSTOMER_ENTERPRISE = 3;

    // Payment method offline
    const TRANSFER = 'TRANSFER';
    const OFFLINE = 'OFFLINE';

    //Declare course_price_currency_id
    const CURRENCY_XU = 1;
    const CURRENCY_VND = 2;

    public $table = 'order';
    protected $dates = ['deleted_at'];

    public $fillable = ['user_id', 'course_id', 'test_id', 'buying_credits', 'reward_credits', 'status', 'classroom_id',
        'course_price_currency_id', 'customer_type_id', 'discount_credits', 'payment_method', 'order_msg'];

    public $timestamps = true;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function exam()
    {
        return $this->belongsTo('App\Models\Test', 'test_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function classroom()
    {
        return $this->belongsTo('App\Models\Classroom', 'classroom_id', 'id');
    }

    /**
     * @param $q
     * @param $search_string
     * @return mixed
     */
    public function scopeSearchString($q, $search_string)
    {
        if (isset($search_string)) {
            return $q->where('buying_credits', 'like', '%' . $search_string . '%');
        }
    }

    public function scopeCustomerType($q, $customer_type_id)
    {
        if (isset($customer_type_id)) {
            return $q->where('customer_type_id', $customer_type_id);
        }
    }

    /**
     * Search by date range
     * @param $q
     * @param $from_date (optional)
     * @param $to_date (optional)
     * @return mixed
     */
    public function scopeDateRange($q, $from_date, $to_date)
    {
        if (isset($from_date) && isset($to_date)) {
            $from_date = date($from_date);
            $to_date = date($to_date);
            return $q->whereBetween('start_date', [$from_date, $to_date]);
        } elseif (isset($from_date) && !isset($to_date)) {
            $from_date = date($from_date);
            return $q->whereDate('start_date', '>=', $from_date);
        } elseif (!isset($from_date) && isset($to_date)) {
            $to_date = date($to_date);
            return $q->whereDate('start_date', '<=', $to_date);
        }
    }
}
