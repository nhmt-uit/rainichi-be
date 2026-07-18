<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExerciseSubmit extends Model
{
    const LOCK = 0;
    const IN_PROGRESS = 1;
    const DONE = 2;
    const NOT_START = 3;
    const PASS_SCORE = 0.6;
    public $table = 'exercise_submit';

    public $fillable = ['user_id', 'score', 'total_question', 'pass', 'lesson_id', 'status', 'course_id'];

    protected $casts = ['pass' => 'boolean'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lesson()
    {
        return $this->belongsTo('App\Models\Lesson', 'lesson_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }
}
