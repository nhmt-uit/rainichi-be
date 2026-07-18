<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchasedCourse extends Model
{
    protected $table = 'purchased_course';

    public $timestamps = false;

    protected $fillable = [
        'course_id',
        'test_id',
        'user_id',
        'start_date',
        'end_date'
    ];

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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id', 'id');
    }
}
