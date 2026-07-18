<?php

namespace App\Http\Controllers\API\Classroom\Course;

use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Test;
use App\Service\BaseResponse;
use App\Transformers\CourseClassAdminTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

class ListController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CourseClassAdminTransformer
     */
    private $courseClassAdminTransformer;

    function __construct(Manager $fractal, CourseClassAdminTransformer $courseClassAdminTransformer)
    {
        $this->fractal = $fractal;
        $this->courseClassAdminTransformer = $courseClassAdminTransformer;
    }

    /**
     * Get Course in classroom
     * @param Request $request
     * @return JsonResponse
     */
    public function getListCourseClass(Request $request)
    {
        $userData = [
            'data' => [],
            'meta' => []
        ];
        $is_course = $request->segment(4) == 'courses' ? true : false;
        $classroom_id = $request->route('id');
        $per_page = $request->query('per_page') ? $request->query('per_page') : 20;
        $courseClassList = CourseClass::query()->with(['classRoom', 'course', 'activeBy', 'approvedBy']);
        if ($is_course) {
            $courseClassList = $courseClassList->join('course', 'course.id', '=', 'course_class.course_id')
                ->where('course.category', Course::COURSE);
        } else {
            $courseClassList = $courseClassList
                ->join('test', 'test.id', '=', 'course_class.test_id');
        }
        $courseClassList = $courseClassList->where('classroom_id', $classroom_id)
            ->select('course_class.*')->orderByRaw('course_class.id desc')->paginate($per_page);
        $courseList = new Collection($courseClassList->items(), $this->courseClassAdminTransformer);
        $courseList->setPaginator(new IlluminatePaginatorAdapter($courseClassList));
        $courseList = $this->fractal->createData($courseList);
        $userData['data'] = $courseList->toArray()['data'];
        $userData['meta'] = $courseList->toArray()['meta'];
        return BaseResponse::customResponse(
            'Get list successfully',
            $userData['data'],
            true,
            200,
            200,
            'Success',
            $userData['meta']
        );
    }
}
