<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCourse extends Model
{
    protected $table = 'user_course';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function coursePrice()
    {
        return $this->belongsTo('App\Models\CoursePrice', 'course_price_id', 'id');
    }
}
