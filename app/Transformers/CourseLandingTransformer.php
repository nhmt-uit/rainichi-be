<?php

namespace App\Transformers;

use App\Models\Course;
use App\Models\CoursePrice;
use League\Fractal\Manager;
use League\Fractal\TransformerAbstract;

class CourseLandingTransformer extends TransformerAbstract
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CourseRouteTransformer
     */
    private $courseRouteTransformer;

    function __construct(Manager $fractal, CourseRouteTransformer $courseRouteTransformer)
    {
        $this->fractal = $fractal;
        $this->courseRouteTransformer = $courseRouteTransformer;
    }

    public function transform(Course $course)
    {
        $coursePrice = CoursePrice::query()
            ->join('course_price_type', 'course_price_type.id', '=', 'course_price.course_price_type_id')
            ->where('course_price.course_id', $course->id)
            ->where('course_price.customer_type_id', $course->customer_type_id)
            ->where('course_price_type.duration', 0)
            ->first();
        return [
            'id' => $course->id,
            'course_type_id' => $course->course_type_id,
            'num_of_employee' => $course->num_of_employee,
            'image' => media_url_web($course->avatar),
            'youtube_link' => $course->youtube_link ? (strpos($course->youtube_link, 'http') !== false ? $course->youtube_link : media_url_web($course->youtube_link)) : null,
            'translations' => $course->getTranslationsArray(),
            'sort_order' => $course->sort_order,
            'prices' => $coursePrice ?? null,
        ];
    }
}
