<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursePrice extends Model
{
    public $timestamps = false;
    protected $table = 'course_price';

    protected $fillable = ['course_id', 'course_price_type_id', 'buying_credits', 'reward_credits', 'is_active',
        'customer_type_id', 'course_price_currency_id', 'landing_buy_credit', 'landing_discount_credit', 'landing_reward_credit', 'discount_credits'];

    ///Declare customer_type_id
    const CUSTOMER_PERSONAL_AND_ENTERPRISE = 1;
    const CUSTOMER_PERSONAL = 2;
    const CUSTOMER_ENTERPRISE = 3;

    protected $casts = [
        'active' => 'boolean'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function priceType()
    {
        return $this->belongsTo('App\Models\CoursePriceType', 'course_price_type_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function currency()
    {
        return $this->hasOne('App\Models\CoursePriceCurrency', 'id', 'course_price_currency_id');
    }

}
