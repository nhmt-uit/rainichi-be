<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Course;


use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CoursePriceCurrency;
use App\Models\CoursePriceType;
use App\Models\CourseType;
use App\Service\BaseResponse;
use App\Transformers\CourseAdminTransformer;
use App\Transformers\CourseLandingTransformer;
use App\Transformers\CoursePriceCurrencyTransformer;
use App\Transformers\CoursePriceTypeTransformer;
use App\Transformers\CourseTransformer;
use App\Transformers\CourseTypeTransformer;
use Illuminate\Http\Request;
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
     * @var CourseTypeTransformer
     */
    private $courseTypeTransformer;
    /**
     * @var CourseTransformer
     */
    private $courseTransformer;
    /**
     * @var CourseAdminTransformer
     */
    private $courseAdminTransformer;
    /**
     * @var CoursePriceTypeTransformer
     */
    private $coursePriceTypeTransformer;
    /**
     * @var CoursePriceCurrencyTransformer
     */
    private $coursePriceCurrencyTransformer;

    /**
     * @var CourseLandingTransformer
     */
    private $courseLandingTransformer;

    function __construct(Manager $fractal, CourseTypeTransformer $courseTypeTransformer, CourseTransformer $courseTransformer,
                         CourseAdminTransformer $courseAdminTransformer, CoursePriceTypeTransformer $coursePriceTypeTransformer,
                         CoursePriceCurrencyTransformer $coursePriceCurrencyTransformer, CourseLandingTransformer $courseLandingTransformer)
    {
        $this->fractal = $fractal;
        $this->courseTypeTransformer = $courseTypeTransformer;
        $this->courseTransformer = $courseTransformer;
        $this->courseAdminTransformer = $courseAdminTransformer;
        $this->coursePriceTypeTransformer = $coursePriceTypeTransformer;
        $this->coursePriceCurrencyTransformer = $coursePriceCurrencyTransformer;
        $this->courseLandingTransformer = $courseLandingTransformer;
    }

    /**
     * Show all list course classify by course type
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $cat = $request->query('category');
        $courses = CourseType::query()->isActive(true)->getByCat($cat)->isDelete(false)->get();
        $courses = new Collection($courses, $this->courseTypeTransformer);
        $courses = $this->fractal->createData($courses);
        return BaseResponse::customResponse(
            'Success',
            $courses->toArray()['data'],
            true,
            200,
            200,
            'Success'
        );
    }

    /**
     * Get all children course by course parent id
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChildrenCourse($id)
    {
        $course_children = Course::getChildrenByParentId($id)->with(['courseType'])->paginate(15);
        $course = new Collection($course_children->items(), $this->courseTransformer);
        $course->setPaginator(new IlluminatePaginatorAdapter($course_children));
        $course = $this->fractal->createData($course);
        if ($course_children) {
            return BaseResponse::customResponse(
                'Success',
                $course->toArray()['data'],
                true,
                200,
                200,
                'Success',
                $course->toArray()['meta']
            );
        } else {
            BaseResponse::customResponse(
                'Course not found',
                [],
                false,
                Config('error_constant.vocabulary.not_found'),
                404,
                'NotFound'
            );
        }
    }

    /**
     * Get course with course has children
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCourseTypeHasChildren()
    {
        $course_children = Course::getCourseWithTypeHasChildren()->get();
        if ($course_children) {
            return BaseResponse::customResponse(
                'Success',
                $course_children,
                true,
                200,
                200,
                'Success'
            );
        } else {
            BaseResponse::customResponse(
                'Course not found',
                [],
                false,
                Config('error_constant.vocabulary.not_found'),
                404,
                'NotFound'
            );
        }
    }

    /**
     * Get list course with filter and transformer data
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListCourseAdmin(Request $request)
    {
        //get query string
        $cat = $request->query('category');
        $search_string = $request->query('search_string');
        $course_type = $request->query('course_type');
        $course_id = $request->query('course_id');
        $created_by = $request->query('created_by');
        $levels = $request->query('levels');
        $lang = $request->query('lang');
        $column = $request->query('column');
        $order_by_type = $request->query('order_by_type');
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $is_shop = $request->query('is_shop');
        $customer_type_id = $request->query('customer_type_id');
        // Get list with filter
        $courses = Course::with(['user', 'prices' => function ($query) use ($is_shop, $customer_type_id) {
            if (isset($is_shop) && $is_shop == 1) {
                $query->where('course_price.is_active', 1);
            }
            if(isset($customer_type_id)) {
                $customer_type = explode( ',' , $customer_type_id);
                $query->whereIn('course_price.customer_type_id', $customer_type);
            }
        }, 'translations'])
            ->searchByString($search_string, $lang)
            ->searchByCourseType($course_type)
            ->searchByCreatedBy($created_by)
            ->searchByCourseId($course_id)
            ->searchByLevel($levels)
            ->orderByInCourse($column, $order_by_type, $is_shop)
            ->orderByName($column, $order_by_type, $lang)
            ->getByCat($cat)
            ->getForShop($is_shop)
            ->customerTypeForShop($customer_type_id)
            ->levelForShop($is_shop)
            ->paginate($paging)
            ->appends($request->query());
        // Create transformer data after get list
        $course = new Collection($courses, $this->courseAdminTransformer);
        $course->setPaginator(new IlluminatePaginatorAdapter($courses));
        $course = $this->fractal->createData($course);
        // Response to json
        return BaseResponse::customResponse(
            'Success',
            $course->toArray()['data'],
            true,
            200, 200,
            'Success',
            $course->toArray()['meta']
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function listPriceType()
    {
        $course_price = CoursePriceType::all();
        $course_price = new Collection($course_price, $this->coursePriceTypeTransformer);
        $course_price = $this->fractal->createData($course_price);
        return BaseResponse::customResponse(
            'Success',
            $course_price->toArray()['data'],
            true,
            200, 200,
            'Success'
        );
    }


    /**
     * @return \Illuminate\Http\JsonResponse
     */

    public function listPriceCurrency()
    {
        $course_currency = CoursePriceCurrency::all();
        $course_currency = new Collection($course_currency, $this->coursePriceCurrencyTransformer);
        $course_currency = $this->fractal->createData($course_currency);
        return BaseResponse::customResponse(
            'Success',
            $course_currency->toArray()['data'],
            true,
            200, 200,
            'Success'
        );
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getParentCourse(Request $request)
    {
        $search_string = $request->query('search_string');
        $lang = $request->query('lang');
        $course_type = $request->query('course_type');
        // Get list with filter
        $courses = Course::with(['translations'])->where('parent_id', 0)
            ->getCourseWithTypeHasChildren()
            ->searchByString($search_string, $lang)
            ->searchByCourseType($course_type)
            ->get();
        // Create transformer data after get list
        $courses = new Collection($courses, $this->courseTransformer);
        $courses = $this->fractal->createData($courses);
        // Response to json
        return BaseResponse::customResponse(
            'Success',
            $courses->toArray()['data'],
            true,
            200, 200,
            'Success',
            []
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCourseLandingPage(Request $request)
    {
        $customer_type_id = $request->query('customer_type_id');
        $customer_type = explode( ',' , $customer_type_id);
        $courses = Course::query()
            ->join('course_type', 'course_type.id', '=', 'course.course_type_id')
            ->where('course_type.category', CourseType::COURSE)
            ->where('course_type.is_active', true)
            ->where('course_type.is_delete', false)
            ->where('course.is_index', true)
            ->where('course.is_active', true)
            ->where('course.parent_id', 0)
            ->where('course.parent_id', 0)
            ->whereIn('course.customer_type_id', $customer_type)
            ->orderByRaw('course.sort_order DESC')
            ->select('course.*')
            ->get();
        // Create transformer data after get list
        $courses = new Collection($courses, $this->courseLandingTransformer);
        $courses = $this->fractal->createData($courses);
        return BaseResponse::customResponse(
            'Success',
            $courses->toArray()['data'],
            true,
            200, 200,
            'Success',
            []
        );
    }
}
