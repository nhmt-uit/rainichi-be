<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseLessonFollowing extends Model
{
    protected $table = 'course_lesson_following';

    public $timestamps = false;

    public $fillable = ['course_lesson_id', 'following_course_lesson_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function course()
    {
        return $this->hasOne('App\Models\CourseLesson', 'id', 'course_lesson_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function course_following()
    {
        return $this->hasOne('App\Models\CourseLesson', 'id', 'following_course_lesson_id');
    }
}
