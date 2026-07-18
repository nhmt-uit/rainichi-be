<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\User;


use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\DefaultAvatar;
use App\Models\ExerciseSubmit;
use App\Models\Order;
use App\Models\OrderByCash;
use App\Models\PurchasedCourse;
use App\Models\User;
use App\Service\BaseResponse;
use App\Transformers\DefaultAvatarTransformer;
use App\Transformers\MyCourseTransformer;
use App\Transformers\MyLessonTransformer;
use App\Transformers\UserTransformer;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
     * @var DefaultAvatarTransformer
     */
    private $avatarTransformer;

    /**
     * @var UserTransformer
     */
    private $userTransformer;

    /**
     * @var MyCourseTransformer
     */

    private $myCourseTransformer;

    /**
     * @var MyLessonTransformer
     */

    private $myLessonTransformer;

    function __construct(Manager $fractal, DefaultAvatarTransformer $avatarTransformer,
                         UserTransformer $userTransformer,
                         MyCourseTransformer $myCourseTransformer,
                         MyLessonTransformer $myLessonTransformer)
    {
        $this->fractal = $fractal;
        $this->avatarTransformer = $avatarTransformer;
        $this->userTransformer = $userTransformer;
        $this->myCourseTransformer = $myCourseTransformer;
        $this->myLessonTransformer = $myLessonTransformer;
    }


    public function index(Request $request)
    {

    }

    /**
     * @return JsonResponse
     */
    public function getListAvatarDefault()
    {
        $avatar = DefaultAvatar::all();
        $avatar = new Collection($avatar, $this->avatarTransformer);
        $avatar = $this->fractal->createData($avatar);
        return BaseResponse::customResponse(
            'Success',
            $avatar->toArray()['data'],
            true,
            200,
            200,
            'Success'
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getListUser(Request $request)
    {
        $is_active = $request->query('is_active');
        $type = $request->query('type');
        $types = $request->query('types');
        $column = $request->query('column') ?? 'id';
        $order_by_type = $request->query('order_by_type') ?? 'desc';
        $search_string = $request->query('search_string');
        $per_page = $request->query('per_page') ? $request->query('per_page') : 20;

        $user_list = User::query()->isActive($is_active)
            ->isAdmin($type)
            ->getMultipleType($types)
            ->searchBy($search_string)
            ->orderByCustom($column, $order_by_type)
            ->paginate($per_page);
        $users = new Collection($user_list, $this->userTransformer);
        $users->setPaginator(new IlluminatePaginatorAdapter($user_list));
        $users = $this->fractal->createData($users);
        return BaseResponse::customResponse(
            'Success',
            $users->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $users->toArray()['meta']
        );
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getListStudents(Request $request)
    {
//        $user_id = Order::query()->where('course_id', '<>', null)->where('status', Order::DONE)->pluck('user_id');
        $search_string = $request->query('search_string');
        $column = $request->query('column');
        $order_by_type = $request->query('order_by_type');
        $per_page = $request->query('per_page') ? $request->query('per_page') : 20;
        $user_list = User::query()
            ->isActive(true)
            ->getMultipleType(json_encode([User::ENTERPRISE, User::USER]))
            ->searchBy($search_string)
            ->orderByCustom($column, $order_by_type)
            ->paginate($per_page);
        $users = new Collection($user_list, $this->userTransformer);
        $users->setPaginator(new IlluminatePaginatorAdapter($user_list));
        $users = $this->fractal->createData($users);
        return BaseResponse::customResponse(
            'Success',
            $users->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $users->toArray()['meta']
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getListTeachers(Request $request)
    {
        $user_list = User::query()->with('infoUser')
            ->isActive(true)
            ->isAdmin(User::TEACHER)->get();
        $users = new Collection($user_list, $this->userTransformer);
        $users = $this->fractal->createData($users);
        return BaseResponse::customResponse(
            'Success',
            $users->toArray()['data'],
            true,
            200,
            200,
            'Success',
            []
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function myCourses(Request $request)
    {
        if ($request->query('user_id')) {
            $user_id = $request->query('user_id');
        } else {
            $user_id = Auth::user()->id;
        }
        $per_page = $request->query('per_page') ? $request->query('per_page') : 20;
        $course_id = PurchasedCourse::query()
            ->where('user_id', $user_id)
            ->where(function ($q) {
                $q->whereDate('end_date', '>=', Carbon::now())
                    ->orWhere('end_date', null);
            })->distinct()
            ->pluck('course_id')->toArray();
        $course_id_by_cash = OrderByCash::query()->where('user_id', $user_id)->pluck('course_id')->toArray();

        $course_id_in_class = CourseClass::query()->availableCourse($user_id)->pluck('course_id')->toArray();
        $course_id = array_merge($course_id, $course_id_in_class, $course_id_by_cash);
        $my_courses = Course::query()->whereIn('id', $course_id)->with(['lessons', 'submit'])->paginate($per_page);
        $this->myCourseTransformer->setUserIdParams($user_id);
        $my_course = new Collection($my_courses, $this->myCourseTransformer);
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
     * @param Request $request
     * @return JsonResponse
     */
    public function myLessonInCourse(Request $request)
    {
        if ($request->query('user_id')) {
            $user_id = $request->query('user_id');
        } else {
            $user_id = Auth::user()->id;
        }
        $per_page = $request->query('per_page') ? $request->query('per_page') : 100;
        $lessons = ExerciseSubmit::query()->with('lesson')->where('user_id', $user_id)
            ->where('course_id', $request->route('course_id'))
            ->whereIn('status', [ExerciseSubmit::IN_PROGRESS, ExerciseSubmit::DONE, ExerciseSubmit::NOT_START])
            ->paginate($per_page);
        $my_course = new Collection($lessons, $this->myLessonTransformer);
        $my_course->setPaginator(new IlluminatePaginatorAdapter($lessons));
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
}
