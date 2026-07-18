<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderByCash extends Model
{
    const PENDING = 0;
    const REJECT = 1;
    const DONE = 2;

    public $table = 'order_by_cash';

    public $fillable = ['user_id', 'course_id', 'test_id', 'payment_method', 'payment_provider', 'payment_transaction_id', 'payment_status', 'payment_msg',
        'discount_code', 'discount_amount', 'amount', 'final_amount'];

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
}
