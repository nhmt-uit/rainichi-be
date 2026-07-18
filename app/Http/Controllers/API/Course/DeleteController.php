<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:59
 */

namespace App\Http\Controllers\API\Course;


use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseRoute;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;

class DeleteController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $list_id = $request->list_id;
        if (isset($list_id)) {
            foreach ($list_id as $id) {
                $course = Course::find($id);
                if ($course) {
                    $old_avatar = $course->avatar;
                    if ($course->delete()) {
                        if ($old_avatar != null) {
                            UploadService::handleRemoveFile($old_avatar);
                        }
                    }
                }
            }

            return BaseResponse::customResponse(
                'Delete successfully',
                [],
                true,
                200,
                202,
                'Accepted'
            );
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function deleteCourseRoute($id)
    {
        $course_route = CourseRoute::query()->find($id);
        if ($course_route) {
            $course_route->delete();
            return BaseResponse::customResponse(
                'Delete successfully',
                [],
                true,
                200,
                202,
                'Accepted'
            );
        } else {
            return BaseResponse::customResponse(
                'Not found',
                [],
                false,
                404,
                404,
                'NotFound'
            );
        }
    }
}
