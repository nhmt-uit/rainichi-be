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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $course_type_data = $request->all();
        $course_type_data['created_by'] = Auth::user()->id;
        $course_type = CourseType::create($course_type_data);
        $language_keys = array_keys($course_type_data['translations']);
        foreach ($language_keys as $language) {
            $course_type->translateOrNew($language)->name = $course_type_data['translations'][$language]['name'];
            $course_type->translateOrNew($language)->description = $course_type_data['translations'][$language]['description'];
        }
        if ($course_type->save()) {
            return BaseResponse::customResponse(
                'Create successfully',
                $course_type,
                true,
                200,
                201,
                'Created'
            );
        } else {
            return BaseResponse::customResponse(
                'Fail to insert',
                [],
                false,
                Config('error_constant.vocabulary.insert_fail'),
                422,
                'Unprocessable Entity'
            );
        }
    }
}
