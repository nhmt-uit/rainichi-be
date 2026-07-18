<?php


namespace App\Http\Controllers\API\Classroom\Statistical;


use App\Http\Controllers\Controller;
use App\Models\CourseClass;
use App\Models\ExerciseSubmit;
use App\Models\TestResult;
use App\Models\User;
use App\Models\UserClass;
use App\Service\BaseResponse;
use App\Transformers\UserClassTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class ListController extends Controller
{
    const DONE = 'DONE';
    const IN_PROGRESS = 'IN_PROGRESS';
    const NEW = 'NEW';

    /**
     * @var Manager
     */
    private $fractal;
    /**
     * @var UserClassTransformer
     */

    private $userClassTransformer;

    function __construct(Manager $fractal, UserClassTransformer $userClassTransformer)
    {
        $this->fractal = $fractal;
        $this->userClassTransformer = $userClassTransformer;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $class_id = $request->route('id');
        $user_in_class = UserClass::query()->with('user')->where('classroom_id', $class_id)->get();
        $user_class = $user_in_class->pluck('user_id');
        $course_class = CourseClass::query()->with(['course.lessons', 'test'])
            ->where('classroom_id', $class_id)
            ->where('is_active', true)
            ->get();
        $course_ids = $course_class->where('course_id', '<>', null)->pluck('course_id');
        $test_ids = $course_class->where('test_id', '<>', null)->pluck('test_id');

        $course_result_dictionary = ExerciseSubmit::query()
            ->whereIn('course_id', $course_ids)
            ->whereIn('user_id', $user_class)
            ->get();

        $test_result_dictionary = TestResult::query()
            ->whereIn('test_id', $test_ids)
            ->whereIn('user_id', $user_class)
            ->orderByDesc('id')->get();
        $course_result = [];
        $test_result = [];
        foreach ($user_class as $user_id) {
            foreach ($course_class->where('course_id', '<>', null) as $course) {
                $c_result = $course_result_dictionary->where('course_id', $course->course_id)->where('user_id', $user_id);
                $final_result = $this->calculateResult($c_result, count($course->course->lessons), $course->course_id);
                array_push($course_result, [$user_id . '-' . $course->course_id => $final_result]);
            }
            foreach ($course_class->where('test_id', '<>', null) as $test) {
                $f_result = $test_result_dictionary->where('test_id', $test->test_id)->where('user_id', $user_id);
                $final_result = $this->testResult($f_result, $test);
                array_push($test_result, [$user_id . '-' . $test->test_id => $final_result]);
            }
        }

        $data = [
            'course_result' => $course_result,
            'course_list' => $course_class,
            'test_result' => $test_result,
            'user_list' => $this->fractal->createData(new Collection($user_in_class, $this->userClassTransformer))->toArray()['data']
        ];
        return BaseResponse::customResponse(
            'Success',
            $data,
            true,
            200,
            200,
            'Success',
            []
        );
    }

    /**
     * @param $submit
     * @param $lesson
     * @param $course_id
     * @return array
     */
    private function calculateResult($submit, $lesson, $course_id)
    {
        $submit_done = count($submit->where('status', ExerciseSubmit::DONE));
        if ($lesson == 0) {
            $result = [
                'id' => $course_id,
                'progress' => 0,
                'status' => self::NEW,
                'result' => self::NEW
            ];
        } elseif ($submit_done / $lesson > 1) {
            $result = [
                'id' => $course_id,
                'progress' => 100,
                'status' => self::DONE,
                'result' => self::DONE
            ];
        } else {
            $progress = ($submit_done / $lesson) * 100;
            $result = [
                'id' => $course_id,
                'progress' => round($progress),
                'status' => $progress == 100 ? self::DONE : ($progress == 0 && !$submit ? self::NEW : self::IN_PROGRESS),
                'result' => $progress == 100 ? self::DONE : ($progress == 0 && !$submit ? self::NEW : self::IN_PROGRESS)
            ];
        }
        return $result;
    }

    /**
     * @param $results
     * @param $test
     * @return array
     */
    private function testResult($results, $test)
    {
        $result = count($results) && isset($results[0]) ? $results[0] : null;
        return [
            'id' => $test->id,
            'type' => $test->type,
            'created_at' => $result ? Carbon::parse($result->created_at)->format("d-m-Y") : null,
            'score' => $result ? round($result->score) : 0,
            'total_question' => $result ? $result->total_question : 0,
            'total_score' => $result ? round($result->total_score) : 0,
            'total_time' => $result ? round($result->total_time) : 0,
            'result' => $result ? ($result->status == 0 ? "FAILED" : "PASSED") : "NONE",
            'test_result' => $results
        ];
    }

}
