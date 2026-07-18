<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;


use App\Models\Course;
use App\Models\OrderByCash;
use App\Models\PurchasedCourse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class CourseAdminTransformer extends TransformerAbstract
{

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CoursePriceTransformer
     */
    private $coursePrice;
    /**
     * @var CourseRouteTransformer
     */
    private $courseRouteTransformer;

    function __construct(Manager $fractal, CoursePriceTransformer $coursePrice, CourseRouteTransformer $courseRouteTransformer)
    {
        $this->fractal = $fractal;
        $this->coursePrice = $coursePrice;
        $this->courseRouteTransformer = $courseRouteTransformer;
    }

    public function transform(Course $course)
    {
        Carbon::useMonthsOverflow(false);
        $duration = null;
        if (Auth::check()) {
            $purchase_cash = OrderByCash::query()
                ->where('course_id', $course->id)
                ->where('user_id', Auth::user()->id)
                ->where('payment_status', OrderByCash::DONE)->first();
            if ($purchase_cash) {
                $duration = 999999;
            } else {
                $purchased_course = PurchasedCourse::query()
                    ->where('course_id', $course->id)
                    ->where('user_id', Auth::user()->id)
                    ->where(function ($q) {
                        $q->whereDate('end_date', '>=', Carbon::now())->orWhere('end_date', null);
                    })
                    ->first();
                if ($purchased_course) {
                    if ($purchased_course->end_date) {
                        $end_date = Carbon::parse($purchased_course->end_date);
                        $duration = $end_date->diff(Carbon::now())->days;
                    } else {
                        $duration = 999999;
                    }

                }
            }
        }
        return [
            'id' => $course->id,
            'avatar' => media_url_web($course->avatar),
            'name' => $course->name,
            'course_type_id' => $course->course_type_id,
            'customer_type_id' => $course->customer_type_id,
            'course_type_name' => $course->course_type_id ? $course->courseType->name : null,
            'youtube_link' => $course->youtube_link ? (strpos($course->youtube_link, 'http') !== false ? $course->youtube_link : media_url_web($course->youtube_link)) : null,
            'is_new' => $course->is_new,
            'is_hot' => $course->is_hot,
            'is_top' => $course->is_top,
            'is_index' => $course->is_index,
            'parent_id' => $course->parent_id,
            'level_id' => $course->level_id,
            'num_of_employee' => $course->num_of_employee,
            'translations' => $course->getTranslationsArray(),
            'course_type' => $course->courseType->getTranslationsArray(),
            'is_active' => $course->is_active,
            'sort_order' => $course->sort_order,
            'course_routes' => $this->fractal->createData(new Collection($course->courseRoute, $this->courseRouteTransformer))->toArray()['data'],
            'prices' => $this->fractal->createData(new Collection($course->prices, $this->coursePrice))->toArray()['data'],
            'duration' => empty($duration) ? null : $duration,
            'created_at' => Carbon::parse($course->created_at)->format('d-m-Y'),
            'created_by' => $course->user ? $course->user->name : '',
        ];
    }
}
