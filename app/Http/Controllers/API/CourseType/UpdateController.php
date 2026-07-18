<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 14:00
 */

namespace App\Http\Controllers\API\CourseType;


use App\Http\Controllers\Controller;
use App\Models\CourseType;
use App\Service\BaseResponse;
use App\Transformers\CourseTypeAdminTransformer;
use Illuminate\Http\Request;
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
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $course_type = CourseType::find($id);
        if ($course_type != null) {
            $course_type_data = $request->all();
            $course_type_data['updated_by'] = Auth::user()->id;
            $course_type->update($course_type_data);
            if ($request->translations) {
                $language_keys = array_keys($course_type_data['translations']);
                foreach ($language_keys as $language) {
                    $course_type->translateOrNew($language)->name = $course_type_data['translations'][$language]['name'];
                    $course_type->translateOrNew($language)->description = $course_type_data['translations'][$language]['description'];
                }
            }
            if ($course_type->save()) {
                return BaseResponse::customResponse(
                    'Update successfully',
                    $course_type,
                    true,
                    200,
                    201,
                    'Updated'
                );
            } else {
                return BaseResponse::customResponse(
                    'Fail to update',
                    [],
                    false,
                    Config('error_constant.vocabulary.insert_fail'),
                    422,
                    'Unprocessable Entity'
                );
            }
        } else {
            return BaseResponse::customResponse(
                'Course type not found',
                [],
                false,
                404,
                404,
                'NotFound'
            );
        }

    }

    public function getCourseTypeById($id)
    {
        $course_type = CourseType::find($id);
        $course_type = new Item($course_type, $this->courseTypeTransformer);
        $course_type = $this->fractal->createData($course_type);
        if ($course_type != null) {
            return BaseResponse::customResponse(
                'Get course type successfully',
                $course_type->toArray()['data'],
                true,
                200,
                200,
                'Success'
            );
        } else {
            return BaseResponse::customResponse(
                'Course type not found',
                [],
                false,
                404,
                404,
                'NotFound'
            );
        }
    }

}
