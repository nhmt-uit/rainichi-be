<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 12/12/2018
 * Time: 15:59
 */

namespace App\Http\Controllers\API\Course;


use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\CourseLesson;
use App\Models\CoursePrice;
use App\Models\CourseTest;
use App\Models\ExerciseSubmit;
use App\Models\Lesson;
use App\Models\OrderByCash;
use App\Models\PurchasedCourse;
use App\Service\BaseResponse;
use App\Transformers\CourseAdminTransformer;
use App\Transformers\CourseTransformer;
use App\Transformers\LessonTransformer;
use App\Transformers\TestAdminTransformer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class DetailController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;
    /**
     * @var CourseTransformer
     */
    private $courseTransformer;
    /**
     * @var CourseAdminTransformer
     */
    private $courseAdminTransformer;
    /**
     * @var TestAdminTransformer
     */
    private $testAdminTransformer;

    /**
     * @var LessonTransformer
     */
    private $lessonTransformer;

    function __construct(Manager $fractal, CourseTransformer $courseTransformer,
                         LessonTransformer $lessonTransformer,
                         CourseAdminTransformer $courseAdminTransformer, TestAdminTransformer $testAdminTransformer)
    {
        $this->fractal = $fractal;
        $this->courseTransformer = $courseTransformer;
        $this->courseAdminTransformer = $courseAdminTransformer;
        $this->lessonTransformer = $lessonTransformer;
        $this->testAdminTransformer = $testAdminTransformer;
    }

    /**
     * Get detail for mobile and client
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail($id)
    {
        $course = Course::where('id', $id)->with(['courseRoute', 'prices' => function($query) {
            $query->where('customer_type_id', CoursePrice::CUSTOMER_PERSONAL);
        }])->first();
        if ($course) {
            $courses = new Item($course, $this->courseTransformer);
            $courses = $this->fractal->createData($courses);
            return BaseResponse::customResponse(
                'Get course by id successfully',
                $courses->toArray()['data'],
                true,
                200,
                200,
                'Success'
            );
        }
    }

    /**
     * Detail for admin
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detailForAdmin($id)
    {
        $course = Course::where('id', $id)->with('courseRoute')->first();
        if ($course) {
            $courses = new Item($course, $this->courseAdminTransformer);
            $courses = $this->fractal->createData($courses);
            return BaseResponse::customResponse(
                'Get course by id successfully',
                $courses->toArray()['data'],
                true,
                200,
                200,
                'Success'
            );
        }
    }

    /**
     * @param $course_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLessonByCourseId($course_id)
    {
        $bought = self::checkCourseAlreadyBought($course_id);
        $this->lessonTransformer->setBought($bought);
        $lesson = CourseLesson::query()->whereHas('lessons', function ($query) {
            $query->where('course_lesson.is_active', true);
        })->where('course_id', $course_id)->orderBy('sort_order')->get();
        if ($lesson && count($lesson->toArray())) {
            $first_lesson_id = $lesson->toArray()[0]['lesson_id'];
            $open_lesson = ExerciseSubmit::query()->where('course_id', $course_id)->where('lesson_id', $first_lesson_id)->where('user_id', Auth::user()->id)->first();
            if (!$open_lesson) {
                ExerciseSubmit::query()->create([
                    'course_id' => $course_id,
                    'lesson_id' => $first_lesson_id,
                    'total_question' => 0,
                    'status' => ExerciseSubmit::NOT_START,
                    'user_id' => Auth::user()->id
                ]);
            }
            $lesson = new Collection($lesson, $this->lessonTransformer);
            $lesson = $this->fractal->createData($lesson);
            return BaseResponse::customResponse(
                'Get lesson by course id successfully',
                $lesson->toArray()['data'],
                true,
                200,
                200,
                'Success'
            );
        } else {
            return BaseResponse::customResponse(
                'Course id not found',
                [],
                false,
                404,
                404,
                'NotFound'
            );
        }
    }

    /**
     * @param $course_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTestByCourseId($course_id)
    {
        $test = CourseTest::query()->whereHas('test', function ($query) {
            $query->where('course_test.is_active', true);
        })->where('course_id', $course_id)->orderBy('sort_order')->get();
        if ($test) {
            $test = new Collection($test, $this->testAdminTransformer);
            $test = $this->fractal->createData($test);
            return BaseResponse::customResponse(
                'Get test by course id successfully',
                $test->toArray()['data'],
                true,
                200,
                200,
                'Success'
            );
        } else {
            return BaseResponse::customResponse(
                'Course id not found',
                [],
                false,
                404,
                404,
                'NotFound'
            );
        }
    }

    private static function checkCourseAlreadyBought($course_id)
    {
        $bought = false;
        # verify user has in class
        $purchase = PurchasedCourse::query()
            ->where('course_id', $course_id)
            ->where('user_id', Auth::user()->id)
            ->where(function ($q) {
                $q->whereDate('end_date', '>=', Carbon::now())->orWhere('end_date', null);
            })
            ->first();
        if ($purchase) {
            $bought = true;
        } else {
            $courseValid = CourseClass::query()->availableCourse(Auth::user()->id, $course_id)->first();
            if ($courseValid) {
                $bought = true;
            }
        }
        $purchase_cash = OrderByCash::query()
            ->where('course_id', $course_id)
            ->where('user_id', Auth::user()->id)
            ->where('payment_status', OrderByCash::DONE)->first();
        if ($purchase_cash) {
            $bought = true;
        }
        return $bought;
    }
}
