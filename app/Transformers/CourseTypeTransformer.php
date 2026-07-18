<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;


use App\Models\Course;
use App\Models\CourseType;
use Carbon\Carbon;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class CourseTypeTransformer extends TransformerAbstract
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CourseTypeTransformer
     */
    private $courseTransformer;

    function __construct(Manager $fractal, CourseTransformer $courseTransformer)
    {
        $this->fractal = $fractal;
        $this->courseTransformer = $courseTransformer;
    }

    public function transform(CourseType $courseType)
    {
        $courses = Course::query()->isActive(true)
            ->personalCourse()
            ->where('course_type_id', $courseType->id)
            ->where('parent_id', 0)->get()->sortBy('sort_order');
        return [
            'id' => $courseType->id,
            'has_course_children' => $courseType->has_course_children,
            'courses' => $this->fractal->createData(new Collection($courses, $this->courseTransformer))->toArray()['data'],
            'translations' => $courseType->getTranslationsArray(),
            'created_at' => Carbon::parse($courseType->created_at)->format('d-m-Y'),
            'created_by' => $courseType->createdBy->name
        ];
    }
}
