<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongstoMany;
use Illuminate\Database\Eloquent\Relations\hasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classroom extends Base
{
    use SoftDeletes;
    protected $table = 'classroom';
    protected $fillable = ['name', 'num_of_employee', 'level_id', 'admin_id', 'is_approved', 'is_active', 'credits',
        'approved_by', 'created_by', 'updated_by', 'company_id', 'is_active'];

    protected $dates = ['deleted_at'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level()
    {
        return $this->belongsTo(Level::class, 'level_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id', 'id')->where('deleted_at', null);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by', 'id');
    }

    /**
     * @return hasMany
     */
    public function userClass()
    {
        return $this->hasMany(UserClass::class, 'classroom_id', 'id');
    }

    /**
     * @return belongstoMany
     */

    public function users()
    {
        return $this->belongstoMany(User::class, 'user_class', 'classroom_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * @return Model User
     */
    public function teacher()
    {
        return $this->users()->where('users.type', User::TEACHER)->first();
    }

    /**
     * @return BelongsToMany User
     */
    public function students()
    {
        return $this->users()->where('users.type', User::ENTERPRISE)->where('users.active', true)->select('users.*');
    }

    /**
     * @return belongstoMany
     */
    public function courses()
    {
        return $this->belongstoMany(Course::class, 'course_class', 'classroom_id', 'course_id')
            ->withTimestamps();
    }

    /**
     * @return hasMany
     */
    public function courseClass()
    {
        return $this->hasMany(CourseClass::class, 'classroom_id', 'id');
    }


    /**
     * Get list by active $is_approved
     * @param $q
     * @param $is_approved
     * @return mixed
     */
    public function scopeIsApproved($q, $is_approved)
    {
        if (isset($status)) {
            if ((boolean)$is_approved === true || (boolean)$is_approved === false)
                return $q->where('is_approved', (boolean)$is_approved);
        }
    }

    /**
     * Get list by active $company_ids
     * @param $q
     * @param $company_ids
     * @return mixed
     */
    public function scopeFilterCompany($q, $company_ids)
    {
        if (!empty($company_ids)) {
            return $q->whereIn('company_id', $company_ids);
        }
    }

    /**
     * @param $value
     * @param $user_id
     * @return bool
     */
    public function updateApproved($value, $user_id)
    {
        return $this->update(['is_approved' => $value, 'approved_by' => $user_id]);
    }

    /**
     * @param $q
     * @param $search_string
     */
    public function scopeSearchName($q, $search_string)
    {
        if ($search_string) {
            $q->where('name', 'like', '%' . $search_string . '%');
        }
    }


}
