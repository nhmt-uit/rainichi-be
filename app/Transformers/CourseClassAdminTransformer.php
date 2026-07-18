<?php

namespace App\Transformers;

use App\Models\CourseClass;
use App\Models\CoursePrice;
use App\Models\CoursePriceType;
use Carbon\Carbon;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class CourseClassAdminTransformer extends TransformerAbstract
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

    /**
     * @var CoursePriceTypeTransformer
     */
    private $coursePriceTypeTransformer;

    function __construct(Manager $fractal, CoursePriceTransformer $coursePrice,
                         CourseRouteTransformer $courseRouteTransformer,
                         CoursePriceTypeTransformer $coursePriceTypeTransformer)
    {
        $this->fractal = $fractal;
        $this->coursePrice = $coursePrice;
        $this->courseRouteTransformer = $courseRouteTransformer;
        $this->coursePriceTypeTransformer = $coursePriceTypeTransformer;
    }

    public function transform(CourseClass $courseClass)
    {
        $course = $courseClass->course;
        $test = $courseClass->test;
        $duration = CoursePriceType::query()->where('duration', $courseClass->duration ?? 0)->first();
        return [
            'id' => $courseClass->id,
            'course_id' => $courseClass->course_id,
            'classroom_id' => $courseClass->classroom_id,
            'avatar' => media_url_web($course->avatar ?? $test->image),
            'name' => $course->name ?? $test->name,
            'translations' => $course ? $course->getTranslationsArray() : $test->getTranslationsArray(),
            'course_type_id' => $course ? $course->course_type_id : null,
            'course_type_name' => $course && $course->course_type_id ? $course->courseType->getTranslationsArray() : null,
            'buying_credits' => $courseClass->buying_credits,
            'reward_credits' => $courseClass->reward_credits,
            'is_active' => $courseClass->is_active,
            'is_approved' => $courseClass->is_approved,
            'is_expired' => $courseClass->is_expired,
            'duration' => (new CoursePriceTypeTransformer)->transform($duration),
            'payment_type' => $courseClass->orderPayment,
            'type' => $courseClass->type,
            'num_of_employee' => $courseClass->num_of_employee,
            'active_date' => $courseClass->active_date ? Carbon::parse($courseClass->active_date)->format('d-m-Y') : null,
            'expired_date' => $courseClass->expired_date ? Carbon::parse($courseClass->expired_date)->format('d-m-Y') : null,
            'created_at' => $courseClass->created_at ? Carbon::parse($courseClass->created_at)->format('d-m-Y') : null,
            'updated_at' => $courseClass->updated_at ? Carbon::parse($courseClass->updated_at)->format('d-m-Y') : null,
            'created_by' => $courseClass->createdBy->name ?? null,
            'active_by' => $courseClass->activeBy->name ?? null,
            'updated_by' => $courseClass->updatedBy->name ?? null,
            'approved_by' => $courseClass->approvedBy->name ?? null,
            'prices' => $course ? $this->fractal->createData(new Collection($course->prices, $this->coursePrice))->toArray()['data'] : $test->enterprise_price
        ];
    }
}
