<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursePromotion extends Base
{
    /**
     * @var string
     */
    public $table = 'course_promotion';

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * @var array
     */
    public $fillable = ['course_id', 'test_id', 'promotion_id', 'is_active'];

    /**
     * @var array
     */
    public $casts = ['is_active' => 'boolean'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courses()
    {
        return $this->hasMany('App\Models\Course');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function test()
    {
        return $this->hasOne('App\Models\Test', 'id', 'test_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function promotion()
    {
        return $this->hasOne('App\Models\Promotion', 'id', 'promotion_id');
    }
}
