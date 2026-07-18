<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 4/24/19
 * Time: 11:14
 */

namespace App\Http\Controllers\API\User;


use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use App\Models\Order;
use App\Models\PurchasedCourse;
use App\Models\Test;
use App\Models\TestResult;
use App\Models\TestResultDetail;
use App\Service\BaseResponse;
use App\Transformers\MyTestDetailTransformer;
use App\Transformers\MyTestTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

class MyTestController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var MyTestTransformer
     */

    private $myTestTransformer;

    /**
     * @var MyTestDetailTransformer
     */

    private $myTestDetailTransformer;

    function __construct(Manager $fractal,
                         MyTestTransformer $myTestTransformer,
                         MyTestDetailTransformer $myTestDetailTransformer)
    {
        $this->fractal = $fractal;
        $this->myTestTransformer = $myTestTransformer;
        $this->myTestDetailTransformer = $myTestDetailTransformer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function myTests(Request $request)
    {
        if ($request->query('user_id')) {
            $user_id = $request->query('user_id');
        } else {
            $user_id = Auth::user()->id;
        }
        $per_page = $request->query('per_page') ? $request->query('per_page') : 20;
        $course_id = PurchasedCourse::query()->where('user_id', $user_id)
            ->where(function ($q) {
                $q->whereDate('end_date', '>=', Carbon::now())->orWhere('end_date', null);
            })
            ->pluck('test_id')->toArray();
        $course_id_in_class = CourseClass::query()->availableCourse($user_id)->pluck('test_id')->toArray();
        $test_result = TestResult::query()->where('user_id', $user_id)->orderByDesc('id')->pluck('test_id')->toArray();
        $course_id = array_merge($course_id, $course_id_in_class, $test_result);
        $my_courses = Test::query()->whereIn('id', $course_id)->with(['result'])->paginate($per_page);
        $this->myTestTransformer->setUserIdParams($user_id);
        $my_course = new Collection($my_courses, $this->myTestTransformer);
        $my_course->setPaginator(new IlluminatePaginatorAdapter($my_courses));
        $my_course = $this->fractal->createData($my_course);
        return BaseResponse::customResponse(
            'Success',
            $my_course->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $my_course->toArray()['meta']
        );
    }

    /**
     * @param $result_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function myResultDetail($result_id)
    {
        $result_detail = TestResultDetail::query()->with('testTime.chapter')->where('test_result_id', $result_id)->get();
        $result_detail = new Collection($result_detail, $this->myTestDetailTransformer);
        $result_detail = $this->fractal->createData($result_detail);
        return BaseResponse::customResponse(
            'Success',
            $result_detail->toArray()['data'],
            true,
            200,
            200,
            'Success',
            []
        );

    }
}
