<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:59
 */

namespace App\Http\Controllers\API\Lesson;


use App\Http\Controllers\Controller;
use App\Models\CourseLesson;
use App\Models\Lesson;
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
                $lesson = Lesson::find($id);
                if ($lesson) {
                    $old_avatar = $lesson->avatar;
                    if ($lesson->delete()) {
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteInCourse(Request $request)
    {
        $list_id = $request->list_id;
        if (isset($list_id)) {
            foreach ($list_id as $id) {
                $lesson = CourseLesson::query()->where('lesson_id', $id)->where('course_id', $request->route('id'));
                if ($lesson) {
                    $lesson->delete();
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
}
