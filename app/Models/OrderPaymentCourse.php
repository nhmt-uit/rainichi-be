<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderPaymentCourse extends Model
{
    //Declare course_price_currency_id
    const CURRENCY_XU = 1;
    const CURRENCY_VND = 2;

    public $table = 'order_payment_course';
    public $fillable = ['classroom_id', 'course_price_currency_id', 'class_name_original',
        'duration', 'course_id', 'test_id', 'order_payment_id', 'num_of_employee'];

    /**
     * @return BelongsTo
     */
    public function classroom()
    {
        return $this->belongsTo('App\Models\Classroom', 'classroom_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function coursePriceCurrency()
    {
        return $this->belongsTo('App\Models\CoursePriceCurrency', 'course_price_currency_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function test()
    {
        return $this->belongsTo('App\Models\Test', 'test_id', 'id');
    }

    /**
     * @return HasOne
     */
    public function orderPayment()
    {
        return $this->hasOne('App\Models\OrderPayment', 'id', 'order_payment_id');
    }

    /**
     * @param $class_id
     * @param $class_name
     * @return bool
     */
    public function updateClassInfo($class_id, $class_name)
    {
       return $this->update(['classroom_id' => $class_id, 'class_name_original' => $class_name]);
    }
}
