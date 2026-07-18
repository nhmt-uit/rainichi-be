<?php

namespace App\Http\Controllers\API\Classroom\Course;


use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Test;
use App\Models\User;
use App\Service\BaseResponse;
use App\Transformers\CoursePriceCurrencyTransformer;
use App\Transformers\CoursePriceTypeTransformer;
use Carbon\Carbon;
use App\Service\ClassroomService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class UpdateController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CoursePriceTypeTransformer
     */
    private $coursePriceType;

    /**
     * @ $coursePriceCurrency
     */
    private $coursePriceCurrency;


    function __construct(Manager $fractal, CoursePriceTypeTransformer $coursePriceType, CoursePriceCurrencyTransformer $coursePriceCurrency)
    {
        $this->fractal = $fractal;
        $this->coursePriceType = $coursePriceType;
        $this->coursePriceCurrency = $coursePriceCurrency;
    }


    /**
     * Get Calculated Price of Course which add to Class
     * @param Request $request
     * @return JsonResponse
     */
    public function getCalculated(Request $request)
    {
        $course_id = $request->query('course_id');
        $num_of_employee = $request->query('num_of_employee');
        $duration = $request->query('duration');
        $course = Course::query()->find($course_id);
        if ($course) {
            $price = $course->prices()
                ->join('course_price_type', 'course_price_type.id', '=', 'course_price.course_price_type_id')
                ->where('course_price_type.duration', $duration)
                ->where('course_price.is_active', 1)
                ->whereIn('course_price.customer_type_id', [Course::CUSTOMER_PERSONAL_AND_ENTERPRISE, Course::CUSTOMER_ENTERPRISE])
                ->first();
            $data = [];
            if ($price) {
                $data = [
                    'price_types' => $this->fractal->createData(new Item($price->priceType, $this->coursePriceType))->toArray()['data'],
                    'currency' => $price->currency ? $price->currency->currency : null,
                    'num_of_employee_course' => (int)$course->num_of_employee,
                    'num_of_employee_class' => (int)$num_of_employee,
                    'customer_type_id' => (int)$course->customer_type_id,
                    'course_type' => (int)$course->customer_type_id == Course::CUSTOMER_ENTERPRISE ? __('Khóa Doanh nghiệp') : __('Cá nhân Trong Doanh nghiệp'),
                    'name' => $course->name,
                    'translations' => $course->getTranslationsArray(),
                    'buying_credits' => self::getPrice($num_of_employee, $price->buying_credits, $course),
                    'reward_credits' => self::getPrice($num_of_employee, $price->reward_credits, $course),
                    'discount_credits' => self::getPrice($num_of_employee, $price->discount_credits, $course),
                ];
            }
            return BaseResponse::customResponse(
                'Get data successfully',
                $data,
                true,
                200,
                200,
                'Success',
                ''
            );
        }
        return BaseResponse::customResponse(
            'Course not found. Please check data',
            '',
            true,
            404,
            404,
            'Not found',
            ''
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getCalculatedExam(Request $request)
    {
        $test_id = $request->query('test_id');
        $num_of_employee = $request->query('num_of_employee');
        $test = Test::query()->find($test_id);
        if ($test) {
            $priceData = [
                'buying_credits' => self::getPrice($num_of_employee, $test->enterprise_price, null, false),
            ];
            return BaseResponse::customResponse(
                'Get data successfully',
                $priceData,
                true,
                200,
                200,
                'Success',
                ''
            );
        }
        return BaseResponse::customResponse(
            'Test not found. Please check data',
            '',
            true,
            404,
            404,
            'Not found',
            ''
        );
    }

    /**
     * Calculated Price of Course & Exam
     * @param $num_of_employee
     * @param $price
     * @param $course
     * @return float|int
     */
    protected function getPrice($num_of_employee, $price, $course, $is_course = true)
    {
        // In this case, If course is exam, we will get the unit of number student * price of the exam. Ignore customer type
        $customer_type = $is_course ? $course->customer_type_id : Course::CUSTOMER_PERSONAL_AND_ENTERPRISE;
        switch ($customer_type) {
            case Course::CUSTOMER_PERSONAL_AND_ENTERPRISE:
                return (float)$price * (int)$num_of_employee;
                break;
            case Course::CUSTOMER_ENTERPRISE:
                $oddDiv = $num_of_employee % $course->num_of_employee;
                $intDiv = intdiv((int)$num_of_employee, (int)$course->num_of_employee);
                $resultDiv = $oddDiv > 0 ? $intDiv + 1 : $intDiv;
                return (float)$price * $resultDiv;
                break;
            default:
                return 0;
        }
    }

    public function update(Request $request)
    {
        $class_course_id = $request->route('class_course_id');
        $data_change = $request->except('_method');
        $course_class = CourseClass::query()->find($class_course_id);
        if ($course_class) {
            // Only Super Admin can edit is_approved enable classroom
            if (key_exists('is_approved', $data_change) && Auth::user()->type <> User::ADMIN
                || !ClassroomService::isApproveOrderPayment($course_class)) {
                // not allow update approved if not Admin system
                unset($data_change['is_approved']);
                return BaseResponse::customResponse(
                    'The order payment is not approve',
                    [],
                    false,
                    Config('error_constant.normal.update_fail'),
                    500,
                    'Not Found'
                );
            }

            // Only Super Admin can edit number student
            if (key_exists('num_of_employee', $data_change) && Auth::user()->type <> User::ADMIN) {
                // not allow update approved if not Admin system
                unset($data_change['num_of_employee']);
            }

            if (key_exists('course_id', $data_change)) {
                //not allow update course_id
                unset($data_change['course_id']);
            }

            if (key_exists('test_id', $data_change)) {
                //not allow update course_id
                unset($data_change['test_id']);
            }

            if (key_exists('is_approved', $data_change) && Auth::user()->type == User::ADMIN) {
                $data_change['approved_by'] = Auth::user()->id;
                if ($data_change['is_approved'])
                    ClassroomService::sendMailApprovedToCompany($course_class);
            }

            if (key_exists('is_active', $data_change) && empty($course_class->active_date)
                && $course_class->is_approved) {
                $data_change['active_date'] = Carbon::now();
                $data_change['active_by'] = Auth::user()->id;
                $data_change['expired_date'] = $course_class->duration > 0 ? Carbon::now()->addMonths($course_class->duration) : null;
            }

            $data_change['updated_by'] = Auth::user()->id;
            $course_class->update($data_change);

            return BaseResponse::customResponse(
                'Update successfully',
                $course_class,
                true,
                200,
                200,
                'Success'
            );
        } else {
            return BaseResponse::customResponse(
                'Data not found',
                [],
                false,
                Config('error_constant.article.not_found'),
                404,
                'Not Found'
            );
        }
    }
}
