<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursePriceCurrency extends Model
{
    protected $table = 'course_price_currency';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function currency()
    {
        return $this->hasMany('App\Models\CoursePrice', 'course_price_currency_id', 'id');
    }
}
