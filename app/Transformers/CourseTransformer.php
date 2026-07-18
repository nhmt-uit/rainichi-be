<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;


use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Order;
use App\Models\OrderByCash;
use App\Models\PurchasedCourse;
use App\Models\UserClass;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class CourseTransformer extends TransformerAbstract
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CourseRouteTransformer
     */
    private $courseRouteTransformer;
    /**
     * @var CoursePriceTransformer
     */
    private $coursePrice;

    function __construct(Manager $fractal, CourseRouteTransformer $courseRouteTransformer, CoursePriceTransformer $coursePrice)
    {
        $this->fractal = $fractal;
        $this->courseRouteTransformer = $courseRouteTransformer;
        $this->coursePrice = $coursePrice;
    }

    public function transform(Course $course)
    {
        $duration = null;
        if (Auth::check()) {
            $purchase_cash = OrderByCash::query()
                ->where('course_id', $course->id)
                ->where('user_id', Auth::user()->id)
                ->where('payment_status', OrderByCash::DONE)->first();
            if ($purchase_cash) {
                $duration = 999999;
            } else {
                # verify user has in class
                $purchase = PurchasedCourse::query()
                    ->where('course_id', $course->id)
                    ->where('user_id', Auth::user()->id)
                    ->where(function ($q) {
                        $q->whereDate('end_date', '>=', Carbon::now())->orWhere('end_date', null);
                    })
                    ->first();
                if ($purchase) {
                    $end_date = $purchase->end_date ? Carbon::parse($purchase->end_date) : null;
                    $duration = (!empty($end_date) ? $end_date->diff(Carbon::now())->days : 999999);
                } else {
                    $courseValid = CourseClass::query()->availableCourse(Auth::user()->id, $course->id)->first();
                    $end_date = null;
                    !empty($courseValid) ? Carbon::parse($courseValid->expired_date) : null;
                    if (!empty($courseValid)) {
                        if ($courseValid->expired_date) {
                            $duration = Carbon::parse($courseValid->expired_date)->diffInDays(Carbon::now());
                        } else {
                            $duration = 999999;
                        }
                    }
                }
            }
        }
        return [
            'id' => $course->id,
            'image' => media_url_web($course->avatar),
            'youtube_link' => $course->youtube_link ? (strpos($course->youtube_link, 'http') !== false ? $course->youtube_link : media_url_web($course->youtube_link)) : null,
            'translations' => $course->getTranslationsArray(),
            'is_new' => $course->is_new,
            'is_hot' => $course->is_hot,
            'is_top' => $course->is_top,
            'is_index' => $course->is_index,
            'level_id' => $course->level_id,
            'level' => $course->levels ? ['translations' => $course->levels->getTranslationsArray()] : null,
            'sort_order' => $course->sort_order,
            'bought' => empty($duration) ? false : true,
            'duration' => $duration,
            'is_foundation' => $course->levels ? $course->levels->is_foundation : false,
            'course_routes' => $course->courseRoute ? $this->fractal->createData(new Collection($course->courseRoute, $this->courseRouteTransformer))->toArray()['data'] : [],
            'prices' => $course->prices ? $this->fractal->createData(new Collection($course->prices, $this->coursePrice))->toArray()['data'] : [],
        ];
    }
}
