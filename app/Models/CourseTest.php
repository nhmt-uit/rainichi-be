<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseTest extends Model
{
    public $table = 'course_test';

    public $timestamps = true;

    public $fillable = ['course_id', 'test_id', 'is_active', 'sort_order'];

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
}
