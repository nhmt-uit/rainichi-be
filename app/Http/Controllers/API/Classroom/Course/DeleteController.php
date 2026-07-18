<?php

namespace App\Http\Controllers\API\Classroom\Course;

use App\Models\CourseClass;
use App\Models\User;
use App\Service\BaseResponse;
use App\Service\ClassroomService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DeleteController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function deleteCourseClass(Request $request)
    {
        $class_course_id = $request->route('class_course_id');
        $course_class = CourseClass::query()->find($class_course_id);
        if ($course_class) {
            if (in_array((int)Auth::user()->type, [User::ADMIN, User::LEADER])) {
                $course_class->delete();
                ClassroomService::sendMailApprovedToCompany($course_class, true);
                return BaseResponse::customResponse(
                    'Delete course in classroom successfully',
                    $course_class,
                    true,
                    200,
                    200,
                    'Success'
                );
            } else {
                return BaseResponse::customResponse(
                    'You not allow delete this course',
                    $course_class,
                    true,
                    403,
                    403,
                    'Forbidden'
                );
            }

        }
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
