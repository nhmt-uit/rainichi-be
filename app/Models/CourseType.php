<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseType extends Model
{
    use \Dimsav\Translatable\Translatable;

    //Declare category
    const COURSE = 1;
    const EXAM = 2;

    protected $table = 'course_type';

    protected $fillable = ['category', 'has_course_children', 'is_active', 'is_delete', 'created_by', 'updated_by'];

    public $translatedAttributes = ['name', 'description'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_delete' => 'boolean',
        'has_course_children' => 'boolean'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courses()
    {
        return $this->hasMany('App\Models\Course');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    /**
     * @param $q
     * @param $is_active
     * @return mixed
     */
    public function scopeIsActive($q, $is_active)
    {
        return $q->where('is_active', $is_active);
    }

    /**
     * @param $q
     * @param $is_delete
     * @return mixed
     */
    public function scopeIsDelete($q, $is_delete)
    {
        return $q->where('is_delete', $is_delete);
    }

    /**
     * @param $q
     * @param $cat
     * @return mixed
     */
    public function scopeGetByCat($q, $cat)
    {
        if (isset($cat))
            return $q->where('category', $cat);
        else
            return $q->where('category', self::COURSE);
    }
}
