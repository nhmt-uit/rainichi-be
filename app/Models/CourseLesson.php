<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLesson extends Model
{
    protected $table = 'course_lesson';

    public $timestamps = true;

    public $fillable = ['course_id', 'lesson_id', 'is_active', 'has_trial', 'sort_order', 'following_lesson'];

    public $casts = ['is_active' => 'boolean', 'has_trial' => 'boolean'];

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
    public function lessons()
    {
        return $this->hasOne('App\Models\Lesson', 'id', 'lesson_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function followings()
    {
        return $this->hasMany('App\Models\CourseLessonFollowing', 'course_lesson_id', 'id');
    }
}
