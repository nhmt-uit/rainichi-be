<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens, SoftDeletes;

    const ADMIN = 1; // Super Admin
    const ENTERPRISE = 2; // User belong to Enterprise
    const USER = 3; // Normal User
    const EDITOR = 4; // Mod Admin
    const LEADER = 5; // Enterprise for Admin page
    const TEACHER = 6; // Teacher

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name', 'email', 'phone', 'type', 'avatar', 'password', 'active', 'activation_token', 'credits', 'email_verified_at', 'level', 'last_access', 'count_access_time'
    ];


    public function getAvatarAttribute()
    {
        return $this->attributes['avatar'] === null || $this->attributes['avatar'] === 'null'
            ? 'users/avatars/avatar-default.png' : $this->attributes['avatar'];
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'activation_token', 'access_tokens', 'deleted_at', 'email_verified_at'
    ];

    protected $casts = [
        'receive_notify' => 'boolean',
        'active' => 'boolean',
        'type' => 'integer'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function levelUser()
    {
        return $this->belongsTo(Level::class, 'level', 'id');
    }

    public function socialUser()
    {
        return $this->hasOne(SocialAccount::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function infoUser()
    {
        return $this->hasOne(UserInfo::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */

    public function applyUser()
    {
        return $this->hasMany(UserApply::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */

    public function userClass()
    {
        return $this->hasMany(UserClass::class, 'user_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */

    public function userCompany()
    {
        return $this->hasMany(UserCompany::class, 'user_id', 'id');
    }

    /**
     * Relate to user type LEADER & ENTERPRISE
     * @return \Illuminate\Database\Eloquent\Relations\belongstoMany
     */
    public function companies()
    {
        return $this->belongstoMany(Company::class, 'user_company', 'user_id', 'company_id')
            ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongstoMany
     */
    public function classroom()
    {
        return $this->belongstoMany(Classroom::class, 'user_class', 'user_id', 'classroom_id')
            ->withTimestamps();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function eventdays()
    {
       return $this->belongsToMany(EventDay::class, 'event_day_users');
    }
    /**
     * @param $q
     * @param $email
     * @return bool
     */
    public function scopeEmailExists($q, $email)
    {
        $check = false;
        $email = $q->where('email', $email)->first();
        if ($email != null) {
            $check = true;
        }
        return $check;
    }

    /**
     * @param $q
     * @param $is_active
     * @return mixed
     */
    public function scopeIsActive($q, $is_active)
    {
        if (isset($is_active)) {
            return $q->where('active', (boolean)$is_active);
        }
    }

    /**
     * @param $q
     * @param $is_admin
     * @return mixed
     */
    public function scopeIsAdmin($q, $is_admin)
    {
        if (isset($is_admin)) {
            return $q->where('type', (int)$is_admin);
        }
    }

    /**
     * @param $q
     * @param $types
     * @return mixed
     */
    public function scopeGetMultipleType($q, $types)
    {
        if (isset($types)) {
            return $q->whereIn('type', json_decode($types));
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function userCourse()
    {
        return $this->belongsToMany('App\Models\Course', 'user_course', 'bought_by', 'course_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function userOrder()
    {
        return $this->belongsToMany('App\Models\Course', 'order', 'user_id', 'course_id');
    }

    /**
     * @param $q
     * @param $search_string
     * @return mixed
     */
    public function scopeSearchBy($q, $search_string)
    {
        if (isset($search_string)) {
            return $q->where('name', 'like', '%' . $search_string . '%')->orWhere('email', 'like', '%' . $search_string . '%')->orWhere('phone', 'like', '%' . $search_string . '%');
        }
    }

    /**
     * @param $q
     * @param $column
     * @param $order_by_type
     * @return mixed
     */
    public function scopeOrderByCustom($q, $column, $order_by_type)
    {
        if (isset($column, $order_by_type)) {
            return $q->orderBy($column, $order_by_type);
        } else {
            return $q->orderBy('created_at', 'desc');
        }
    }

    /**
     * access token, we need to logout users
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accessTokens()
    {
        return $this->hasMany(OauthAccessToken::class, 'user_id', 'id');
    }

    /**
     * update user to User::LEADER
     * @return bool User
     */

    public function updateLeaderType()
    {
        return $this->update(['type' => self::LEADER]);
    }

    /**
     * @return bool
     */
    public function isUser()
    {
        return $this->type == self::USER;
    }

    /**
     * @return bool
     */
    public function isLeader()
    {
        return $this->type == self::LEADER;
    }

    public function isAdmin()
    {
        return $this->type == self::ADMIN;
    }    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function notification_user_list()
    {
        return $this->belongsToMany(Notification::class, 'notification_group');
    }

}
