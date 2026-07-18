<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseClass extends Base
{
    use  SoftDeletes;
    protected $table = 'course_class';
    protected $fillable = ['course_id', 'test_id', 'classroom_id', 'is_active', 'created_by', 'updated_by',
        'duration', 'buying_credits', 'reward_credits', 'purchase_date', 'active_date', 'expired_date',
        'is_expired', 'is_approved', 'active_by', 'approved_by', 'num_of_employee', 'type', 'approved_at',
        'order_payment_id'
    ];

    //Declare type
    const ADD_BY_DEFAULT = 1;
    const ADD_BY_ADMIN = 2;

    protected $casts = [
        'is_active' => 'boolean',
        'is_approved' => 'boolean',
        'is_expired' => 'boolean'
    ];
    protected $dates = ['deleted_at'];

    /**
     * @return BelongsTo
     */
    public function classRoom()
    {
        return $this->belongsTo(Classroom::class, 'classroom_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function orderPayment()
    {
        return $this->belongsTo(OrderPayment::class, 'order_payment_id', 'id')->select('payment_method');
    }
    /**
     * @return BelongsTo
     */
    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function activeBy()
    {
        return $this->belongsTo(User::class, 'active_by', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by', 'id');
    }

    /**
     * @param $q
     * @param $courseType
     * @return Model Course
     */
    public function scopeClassCourses($q, $courseType)
    {
        return $q->course()->where('course.category', $courseType)->where('course.is_active', true);
    }

    /**
     * @param $q
     * @param $column
     * @param $order_by_type
     * @return mixed
     */
    public function scopeOrderByCustom($q, $column, $order_by_type)
    {
        return $q->orderBy($column, $order_by_type);
    }

    /**
     * Get list by is_expired status
     * @param $q
     * @param bool $is_expired
     * @return mixed
     */
    public function scopeIsExpired($q, $is_expired)
    {
        if (isset($is_expired)) {
            if ((boolean)$is_expired === true || (boolean)$is_expired === false)
                return $q->where('is_expired', (boolean)$is_expired);
        }
    }

    /**
     * Get list by is_approved status
     * @param $q
     * @param $is_approved
     * @return mixed
     */
    public function scopeIsApproved($q, $is_approved)
    {
        if (isset($is_approved)) {
            if ((boolean)$is_approved === true || (boolean)$is_approved === false)
                return $q->where('is_approved', (boolean)$is_approved);
        }
    }

    /**
     * @param $q
     * @param $user_id
     * @param null $course_id
     * @param null $test_id
     * @return mixed
     */
    public function scopeAvailableCourse($q, $user_id, $course_id = null, $test_id = null)
    {
        if (isset($user_id)) {
            return $q->join('classroom', 'classroom.id', '=', 'course_class.classroom_id')
                ->join('user_class', 'user_class.classroom_id', '=', 'classroom.id')
                ->where('course_class.is_active', true)->where('course_class.is_approved', true)
                ->where('course_class.is_expired', false)
                ->where('classroom.is_active', true)
                ->where('user_class.is_active', true)
                ->where('user_class.user_id', $user_id)
                ->whereDate('active_date', '<=', Carbon::now())
                ->where(function ($q) {
                    $q->whereDate('expired_date', '>=', Carbon::now())
                        ->orWhere('expired_date', null);
                })
                ->where(function ($q) use ($course_id, $test_id) {
                    if (isset($course_id)) {
                        $q->where('course_class.course_id', $course_id);
                    }
                    if (isset($test_id)) {
                        $q->where('course_class.test_id', $test_id);
                    }
                })->select('course_class.*');
        }

    }

    /**
     * @param $value
     * @param $user_id
     * @return bool
     */
    public function updateApproved($value, $user_id)
    {
        return $this->update(
            ['is_approved' => $value,
                'approved_by' => $user_id,
                'approved_at' => Carbon::now()
            ]);
    }
}
