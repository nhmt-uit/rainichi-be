<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Base
{
    const ACTIVE = 1;

    //Declare category
    const COURSE = 1;
    const EXAM = 2;

    //Declare customer_type_id
    const CUSTOMER_PERSONAL_AND_ENTERPRISE = 1;
    const CUSTOMER_PERSONAL = 2;
    const  CUSTOMER_ENTERPRISE = 3;

    use \Dimsav\Translatable\Translatable;

    public $timestamps = true;

    protected $table = 'course';

    public $translatedAttributes = ['name', 'slug', 'description', 'landing_description', 'landing_gift'];

    protected $fillable = [
        'category',
        'course_type_id',
        'customer_type_id',
        'level_id',
        'avatar',
        'youtube_link',
        'is_active',
        'is_new',
        'is_top',
        'is_hot',
        'sort_order',
        'parent_id',
        'created_by',
        'updated_by',
        'num_of_employee',
        'is_index'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_new' => 'boolean',
        'is_top' => 'boolean',
        'is_hot' => 'boolean',
        'is_index' => 'boolean'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courseType()
    {
        return $this->belongsTo('App\Models\CourseType');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prices()
    {
        return $this->hasMany('App\Models\CoursePrice', 'course_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function lessons()
    {
        return $this->belongsToMany('App\Models\Lesson', 'course_lesson', 'course_id', 'lesson_id')
            ->withPivot('has_trial', 'sort_order', 'is_active')->orderBy('course_lesson.sort_order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function submit()
    {
        return $this->hasMany(ExerciseSubmit::class, 'course_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courseRoute()
    {
        return $this->hasMany(CourseRoute::class, 'course_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function levels()
    {
        return $this->belongsTo('App\Models\Level', 'level_id', 'id');
    }

    /**
     * Handle for Enterprise solutions
     * @return \Illuminate\Database\Eloquent\Relations\belongstoMany
     */
    public function classroom()
    {
        return $this->belongstoMany(Classroom::class, 'course_class', 'course_id', 'classroom_id')
            ->withTimestamps();
    }

    /**
     * Handle for Enterprise solutions
     * @return \Illuminate\Database\Eloquent\Relations\belongstoMany
     */
    public function promotion()
    {
        return $this->belongstoMany(Promotion::class, 'course_promotion', 'course_id', 'promotion_id')
            ->withTimestamps();
    }


    /**
     * @param $customer_type_id
     * @return \Illuminate\Database\Eloquent\Model Pirce
     */
    public function priceByCustomer($customer_type_id)
    {
        return $this->prices()->where('customer_type_id', $customer_type_id)->get();
    }

    /**
     * @param $q
     * @param $is_shop
     * @return mixed
     */
    public function scopeLevelForShop($q, $is_shop)
    {
        if (isset($is_shop) && $is_shop == 1)
            return $q->whereHas('levels', function ($query) {
                $query->where('is_foundation', '<>', 1);
            });
    }

    /**
     * Get parent course
     * @param $q
     * @return mixed
     */
    public function scopeIsParent($q)
    {
        return $q->where('parent_id', 0);
    }


    /**
     * Get children course by parent id
     * @param $q
     * @param $parent_id
     * @return mixed
     */
    public function scopeGetChildrenByParentId($q, $parent_id)
    {
        if (isset($parent_id)) {
            return $q->where('parent_id', $parent_id)->where('is_active', true);
        }
    }

    /**
     * Get all course with course type has course children
     * @param $q
     * @return mixed
     */
    public function scopeGetCourseWithTypeHasChildren($q)
    {
        return $q->whereHas('courseType', function ($query) {
            return $query->where('has_course_children', true);
        });
    }

    /**
     * @param $q
     * @param $user_id
     * @return mixed
     */
    public function scopeSearchByCreatedBy($q, $user_id)
    {
        if (isset($user_id)) {
            return $q->where('created_by', (int)$user_id);
        }
    }

    /**
     * @param $q
     * @param $course_type_id
     * @return mixed
     */
    public function scopeSearchByCourseType($q, $course_type_id)
    {
        if (isset($course_type_id) && trim($course_type_id)) {
            return $q->where('course_type_id', (int)$course_type_id);
        }
    }

    /**
     * @param $q
     * @param $search_string
     * @param $lang
     * @return mixed
     */
    public function scopeSearchByString($q, $search_string, $lang)
    {
        if (isset($search_string) && isset($lang)) {
            return $q->whereHas(
                'translations',
                function ($query) use ($search_string, $lang) {
                    $query->where('name', 'like', '%' . $search_string . '%');
                    $query->where('locale', $lang);
                }
            );
        }
    }

    /**
     * @param $q
     * @param $level
     * @return mixed
     */
    public function scopeSearchByLevel($q, $level)
    {
        if (isset($level) && trim($level)) {
            return $q->whereIn('level_id', json_decode($level));
        }
    }

    /**
     * @param $q
     * @return mixed
     */
    public function scopeGetActiveCourse($q)
    {
        return $q->where('is_active', self::ACTIVE);
    }

    /**
     * @param $q
     * @param $column
     * @param $order_by_type
     * @param $is_shop
     * @return mixed
     */
    public function scopeOrderByInCourse($q, $column, $order_by_type, $is_shop)
    {
        if (isset($column, $order_by_type)) {
            if ($column !== 'name' && $column !== 'course_type_name') {
                return $q->orderBy($column, $order_by_type);
            }
        } else if(isset($is_shop) && $is_shop == 1) {
            return $q->orderBy('course_type_id', 'asc')->orderBy('sort_order');
        } else {
            return $q->orderBy('id', 'desc');
        }
    }

    /**
     * @param $q
     * @param $column
     * @param $order_by_type
     * @param $lang
     * @return mixed
     */
    public function scopeOrderByName($q, $column, $order_by_type, $lang)
    {
        if (isset($column) && isset($order_by_type) && isset($lang)) {
            if ($column === 'name') {
                return $q->join('course_translations as t', function ($join) use ($lang) {
                    $join->on('course.id', '=', 't.course_id')
                        ->where('t.locale', '=', $lang);
                })->groupBy('course.id')
                    ->orderBy('t.name', $order_by_type)->select('course.*');
            }
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function userCourse()
    {
        return $this->belongsToMany('App\Models\User', 'user_course', 'course_id', 'bought_by');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function userOrder()
    {
        return $this->belongsToMany('App\Models\Order', 'order', 'course_id', 'user_id');
    }

    /**
     * @param $q
     * @param $cat
     * @return mixed
     */
    public function scopeGetByCat($q, $cat)
    {
        if (isset($cat))
            return $q->where('course.category', $cat);
        else
            return $q->where('course.category', self::COURSE);
    }

    /**
     * @param $q
     * @param $course_id
     * @return mixed
     */
    public function scopeSearchByCourseId($q, $course_id)
    {
        if (isset($course_id))
            return $q->where('course.id', $course_id);
    }

    /**
     * @param $q
     * @param $is_shop
     */
    public function scopeGetForShop($q, $is_shop)
    {
        if (isset($is_shop) && $is_shop == 1) {
            $q->join('course_type', 'course.course_type_id', '=', 'course_type.id')
                ->where('course_type.has_course_children', '<>', 0)->where('course.parent_id', '<>', 0)
                ->orWhere('course_type.has_course_children', 0)->where('course.parent_id', 0)
                ->select('course.*');
        }
    }

    /**
     * @param $q
     * @param $customer_type_id
     * @return mixed
     */
    public function scopeCustomerTypeForShop($q, $customer_type_id)
    {
        $customer_type = explode( ',' , $customer_type_id);
        if (isset($customer_type_id) && trim($customer_type_id) && is_array($customer_type)) {
            return $q->whereIn('customer_type_id', $customer_type);
        }
    }

    public function scopePersonalCourse($q)
    {
        $types = [self::CUSTOMER_PERSONAL_AND_ENTERPRISE, self::CUSTOMER_PERSONAL];
        return $q->whereIn('customer_type_id', $types);
    }
}
