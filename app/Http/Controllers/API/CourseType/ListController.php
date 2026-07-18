<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\CourseType;


use App\Http\Controllers\Controller;
use App\Models\CourseType;
use App\Service\BaseResponse;
use App\Transformers\CourseTypeAdminTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class ListController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CourseTypeAdminTransformer
     */
    private $courseTypeTransformer;

    function __construct(Manager $fractal, CourseTypeAdminTransformer $courseTypeTransformer)
    {
        $this->fractal = $fractal;
        $this->courseTypeTransformer = $courseTypeTransformer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $cat = $request->query('category');
        $course_type_list = CourseType::with('createdBy')->isDelete(false)->getByCat($cat)->orderByDesc('id')->get();
        $course_type_list = new Collection($course_type_list, $this->courseTypeTransformer);
        $course_type_list = $this->fractal->createData($course_type_list);
        return BaseResponse::customResponse(
            'Get list course type successfully',
            $course_type_list->toArray()['data'],
            true,
            200, 200,
            'Success'
        );
    }
}
