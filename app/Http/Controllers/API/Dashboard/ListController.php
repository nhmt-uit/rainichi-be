<?php

namespace App\Http\Controllers\API\Dashboard;

use App\Models\Classroom;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\Test;
use App\Models\User;
use App\Models\UserClass;
use App\Service\BaseResponse;
use App\Transformers\OrderPaymentEnterpriseTransformer;
use App\Transformers\OrderPaymentTransformer;
use App\Transformers\OrderTransformer;
use App\Transformers\TransactionTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
     * @var OrderPaymentEnterpriseTransformer
     */
    private $orderPaymentEnterpriseTransformer;
    /**
     * @var UserTransformer
     */
    private $userTransformer;
    /**
     * @var OrderTransformer
     */
    private $orderTransformer;

    /**
     * ListController constructor.
     * @param Manager $fractal
     * @param OrderPaymentEnterpriseTransformer $orderPaymentEnterpriseTransformer
     * @param OrderTransformer $orderTransformer
     * @param UserTransformer $userTransformer
     */
    function __construct(Manager $fractal,
                         OrderPaymentEnterpriseTransformer $orderPaymentEnterpriseTransformer,
                         OrderTransformer $orderTransformer,
                         UserTransformer $userTransformer)
    {
        $this->fractal = $fractal;
        $this->orderPaymentEnterpriseTransformer = $orderPaymentEnterpriseTransformer;
        $this->userTransformer = $userTransformer;
        $this->orderTransformer = $orderTransformer;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getTotal(Request $request)
    {
        $totalOrderPayment = OrderPayment::query()->where('payment_status', OrderPayment::DONE)->count();
        $totalOrder = Order::query()->where('status', Order::DONE)->count();
        $totalStudent = User::query()->whereIn('type', [User::ENTERPRISE, User::USER])->count();
        $totalCourse = Course::query()->whereIn('category', [Course::COURSE])->count();
        $totalTest = Test::query()->count();
        $totalClassroom = Classroom::query()->count();
        $data = [
            'totalPayment' => $totalOrderPayment,
            'totalOrder' => $totalOrder,
            'totalStudent' => $totalStudent,
            'totalCourse' => $totalCourse,
            'totalTest' => $totalTest,
            'totalClassroom' => $totalClassroom,
        ];
        return BaseResponse::customResponse(
            'Get list successfully',
            $data,
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
    public function bestSellingCourse(Request $request)
    {
        $type = $request->query('type');
        $format = '%Y-%m-%d';
        switch ($type) {
            case 'day' :
                $format = '%Y-%m-%d';
                break;
            case 'month':
                $format = '%Y-%m';
                break;
            case 'year':
                $format = '%Y';
                break;
        }
        $number = $request->query('number');
        $order_list = DB::table('course_class')
            ->select(DB::raw("DATE_FORMAT(created_at, '" . $format . "') as created_at"), 'course_id', DB::raw('count(course_id) as quantity'))
            ->groupBy(DB::raw("course_id"), DB::raw("DATE_FORMAT(created_at, '" . $format . "')"))
            ->orderByDesc('created_at')
            ->whereNotNull('course_id')
            ->take($number)
            ->get();
        $data = [];
        foreach ($order_list as $order) {
            $temp = [];
            $course = Course::query()->find($order->course_id);
            $temp['id'] = $course->id;
            $temp['name'] = $course->name;
            $temp['translator'] = $course->getTranslationsArray();
            $temp['quantity'] = $order->quantity;
            $temp['created_at'] = $order->created_at;
            array_push($data, $temp);
        }
        return BaseResponse::customResponse(
            'Get list successfully',
            $data,
            true,
            200,
            200,
            'Success',
            []
        );
    }

    public function recentOrderClass(Request $request)
    {
        $number = $request->query('number');
        $order_list = Order::query()
            ->with(['user'])
            ->orderByDesc('id')
            ->take($number)
            ->get();
        $orders = new Collection($order_list, $this->orderTransformer);
        $orders = $this->fractal->createData($orders);
        return BaseResponse::customResponse(
            'Get list successfully',
            $orders->toArray()['data'],
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
    public function recentStudents(Request $request)
    {
        $number = $request->query('number');
        $user_list = null;
        if (Auth::user()->type === User::ADMIN) {
            $user_list = User::query()->whereIn('type', [User::USER, User::ENTERPRISE])
                ->isActive(true)
                ->orderByDesc('id')
                ->take($number)->get();
        } else {
            $class_id = Classroom::query()->where('admin_id' === Auth::user()->id)->pluck('id');
            $user_class = UserClass::query()->whereIn('classroom_id', $class_id)->pluck('user_id');
            $user_list = User::query()
                ->whereIn('id', $user_class)
                ->get();
        }
        $users = new Collection($user_list, $this->userTransformer);
        $users = $this->fractal->createData($users);
        return BaseResponse::customResponse(
            'Get list user successfully',
            $users->toArray()['data'],
            true,
            200,
            200,
            'Success',
            []
        );
    }
}
