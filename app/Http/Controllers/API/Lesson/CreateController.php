<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Lesson;


use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\LessonRequest;
use App\Models\CourseLesson;
use App\Models\Lesson;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{
    /**
     * @param LessonRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(LessonRequest $request)
    {
        $lesson_data = $request->all();

        if ($request->hasFile('avatar')) {

            $avatar = UploadService::handleUploadFile($request->file('avatar'), Config('uploadpath.lessons_folder'));
            $lesson_data['avatar'] = $avatar;
        }
        if ($lesson_data['level_id'] == 0) {
            $lesson_data['level_id'] = null;
        }
        $lesson_data['created_by'] = Auth::user()->id;
        $lesson = Lesson::create($lesson_data);

        // get translation keys and add to translation table
        $language_keys = array_keys($lesson_data['translations']);
        foreach ($language_keys as $language) {
            $lesson->translateOrNew($language)->name = $lesson_data['translations'][$language]['name'];
            $lesson->translateOrNew($language)->slug = Str::slug($lesson_data['translations'][$language]['name']);
            $lesson->translateOrNew($language)->description = $lesson_data['translations'][$language]['description'];
        }
        if (array_key_exists('chapters', $lesson_data) && $lesson_data['chapters']) {
            $lesson->chapters()->sync($lesson_data['chapters']);
        }
        if ($lesson->save()) {
            if ($request->has('course_id')) {
                CourseLesson::query()->create([
                    'course_id' => $request->get('course_id'),
                    'lesson_id' => $lesson->id,
                    'is_active' => $request->get('is_active_in_course')
                ]);
            }
            return BaseResponse::customResponse(
                'Create successfully',
                $lesson,
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
                Config('error_constant.lesson.insert_fail'),
                422,
                'Unprocessable Entity'
            );
        }
    }
}
