<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Test extends Model
{
    //Declare test type
    const JLPT = 1;
    const LT = 2;

    //Declare customer_type_id
    const CUSTOMER_PERSONAL_AND_ENTERPRISE = 1;
    const CUSTOMER_PERSONAL = 2;
    const  CUSTOMER_ENTERPRISE = 3;

    use \Dimsav\Translatable\Translatable;

    public $table = 'test';

    protected $translatedAttributes = ['name', 'description'];

    public $timestamps = true;

    protected $fillable = [
        'price',
        'reward',
        'minimum_score',
        'video',
        'image',
        'is_active',
        'type',
        'is_combine',
        'level_id',
        'created_by',
        'updated_by',
        'discount_price',
        'enterprise_price',
        'enterprise_discount_price',
        'customer_type_id',
        'sort_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_combine' => 'boolean'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function levels()
    {
        return $this->belongsTo('App\Models\Level', 'level_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function testTimes()
    {
        return $this->hasMany('App\Models\TestTimes', 'test_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function testFail()
    {
        return $this->hasMany('App\Models\TestFail', 'test_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function chapters()
    {
        return $this->belongsToMany('App\Models\Chapter', 'test_chapter', 'test_id', 'chapter_id')->orderBy('id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function courses()
    {
        return $this->belongsToMany('App\Models\Course', 'course_test', 'test_id', 'course_id')
            ->withPivot('course_test.sort_order', 'is_active')->orderBy('course_test.sort_order');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function result()
    {
        return $this->hasMany(TestResult::class, 'test_id', 'id')->where('user_id', Auth::user()->id);
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany('App\Models\Order', 'test_id', 'id');
    }


    /**
     * @param $q
     * @param $level_id
     * @return mixed
     */
    public function scopeGetByLevel($q, $level_id)
    {
        if (isset($level_id) && trim($level_id) && count(json_decode($level_id)) > 0) {
            return $q->whereIn('test.level_id', json_decode($level_id));
        }
    }

    /**
     * @param $q
     * @param $created_by
     * @return mixed
     */
    public function scopeGetByUser($q, $created_by)
    {
        if (isset($created_by)) {
            return $q->where('created_by', (int)$created_by);
        }
    }

    /**
     * @param $q
     * @param $search_string
     * @param $lang
     * @return mixed
     */
    public function scopeSearchString($q, $search_string, $lang)
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
     * @param $column
     * @param $order_by_type
     * @param $lang
     * @return mixed
     */
    public function scopeOrderByName($q, $column, $order_by_type, $lang)
    {
        if (isset($column) && isset($order_by_type) && isset($lang)) {
            if ($column === 'name') {
                return $q->join('test_translations as t', function ($join) use ($lang) {
                    $join->on('test.id', '=', 't.test_id')
                        ->where('t.locale', '=', $lang);
                })->groupBy('test.id')
                    ->orderBy('t.name', $order_by_type)->select('test.*');
            }
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
            if ($column === 'sort_order') {
                $column = 'test.sort_order';
            }
            return $q->orderBy($column, $order_by_type);
        }
    }

    /**
     * @param $q
     * @param $lesson
     * @param $course_id
     * @param $is_cms
     * @return mixed
     */
    public function scopeGetByCourseId($q, $lesson, $course_id, $is_cms)
    {
        if (isset($lesson)) {
            $sort = !isset($is_cms) && $is_cms != 1 ? 'asc' : 'desc';
            return $q->whereIn('test.id', $lesson)->leftJoin('course_test as c', 'c.test_id', '=', 'test.id')
                ->where('c.course_id', $course_id)->select('c.sort_order', 'c.course_id', 'c.is_active', 'test.*')->orderBy('c.sort_order', $sort);
        }
    }

    /**
     * @param $q
     * @param $test_list
     * @param $course_id
     * @return mixed
     */
    public function scopeOrderByCourse($q, $test_list = null, $course_id = null)
    {
        if (isset($test_list) && isset($course_id)) {
            return $q->whereIn('test.id', $test_list)->leftJoin('course_test as c', 'c.test_id', '=', 'test.id')
                ->where('c.course_id', $course_id)->whereIn('test.customer_type_id', [self::CUSTOMER_PERSONAL, self::CUSTOMER_PERSONAL_AND_ENTERPRISE])->select('c.sort_order', 'c.course_id', 'c.is_active', 'test.*')->orderBy('c.sort_order');
        } else {
            return $q->leftJoin('course_test as ct', 'ct.test_id', '=', 'test.id')
                ->leftJoin('course as c', 'c.id', '=', 'ct.course_id')
                ->select('test.*')->orderBy('c.course_type_id')->orderBy('c.sort_order');
        }
    }

    /**
     * @param $q
     * @param $chapter_id
     * @return mixed
     */
    public function scopeGetByChapterId($q, $chapter_id)
    {
        if (isset($chapter_id)) {
            return $q->leftJoin('test_chapter as c', 'c.test_id', '=', 'test.id')
                ->where('c.chapter_id', $chapter_id)->select('test.*');
        }
    }

    /**
     * @param $q
     * @param $type
     * @return mixed
     */
    public function scopeGetByType($q, $type)
    {
        if (isset($type)) {
            return $q->where('test.type', $type);
        }
    }

    /**
     * @param $q
     * @param $course_type_id
     * @return mixed
     */
    public function scopeGetByCourseType($q, $course_type_id)
    {
        if (isset($course_type_id)) {
            $course_ids = Course::query()->where('course_type_id', $course_type_id)->pluck('id');
            if ($course_ids) {
                $test_id = CourseTest::query()->whereIn('course_id', $course_ids)->pluck('test_id');
                return $q->whereIn('test.id', $test_id);
            }
        }
    }

    /**
     * @param $q
     * @param $test_id
     * @return mixed
     */
    public function scopeGetByTestId($q, $test_id)
    {
        if (isset($test_id)) {
            return $q->where('test.id', $test_id);
        }
    }

    /**
     * @param $q
     * @param $customer_type_id
     * @return mixed
     */
    public function scopeGetCustomerType($q, $customer_type_id)
    {
        $customer_type = explode(',', $customer_type_id);
        if (isset($customer_type_id) && trim($customer_type_id) && is_array($customer_type)) {
            return $q->whereIn('test.customer_type_id', $customer_type);
        }
    }

    public function getTotalQuestion()
    {
        $totalQuestion = 0;
        $testTime = $this->testTimes->pluck('id');
        foreach ($testTime as $tt) {
            $questionsData = Question::getQuestionsByTestTime($tt);
            $totalQuestion += array_sum(array_column($questionsData->toArray(), 'total_question'));
        }
        return $totalQuestion;
    }

    public function scopeGetActive($q, $is_shop) {
        if(isset($is_shop)) {
            return $q->where("test.is_active", true);
        }
    }
}
