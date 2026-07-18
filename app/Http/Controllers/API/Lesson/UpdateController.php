<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 14:00
 */

namespace App\Http\Controllers\API\Lesson;


use App\Http\Controllers\Controller;
use App\Models\CourseLesson;
use App\Models\Lesson;
use App\Service\BaseResponse;
use App\Service\UploadService;
use App\Transformers\LessonAdminTransformer;
use Carbon\Carbon;
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
     * @var LessonAdminTransformer
     */
    private $lessonAdminTransformer;

    function __construct(Manager $fractal, LessonAdminTransformer $lessonAdminTransformer)
    {
        $this->fractal = $fractal;
        $this->lessonAdminTransformer = $lessonAdminTransformer;
    }

    /**
     * Update lesson
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $lesson = Lesson::find($request->route('id'));
        if ($lesson) {
            $lesson_data = $request->except('_method');

            if ($request->hasFile('avatar')) {
                $old_avatar = $lesson->avatar;
                $avatar = UploadService::handleUploadFile($request->file('avatar'), Config('uploadpath.lessons_folder'));
                $lesson_data['avatar'] = $avatar;
                if ($old_avatar != null) {
                    UploadService::handleRemoveFile($old_avatar);
                }
            }
            if ($lesson_data['level_id'] == 0) {
                $lesson_data['level_id'] = null;
            }
            $lesson_data['updated_by'] = Auth::user()->id;
            $lesson->update($lesson_data);
            // get translation keys and add to translation table
            if ($request->translations) {
                $language_keys = array_keys($lesson_data['translations']);
                foreach ($language_keys as $language) {
                    $lesson->translateOrNew($language)->name = $lesson_data['translations'][$language]['name'];
                    $lesson->translateOrNew($language)->slug = str_slug($lesson_data['translations'][$language]['name']);
                    $lesson->translateOrNew($language)->description = $lesson_data['translations'][$language]['description'];
                }
            }
            if ($request->chapters) {
                $lesson->chapters()->sync($lesson_data['chapters']);
            }
            if ($lesson->save()) {
                if ($request->has('course_id')) {
                    CourseLesson::query()->where('course_id', $request->get('course_id'))->where('lesson_id', $lesson->id)->first()->update([
                        'is_active' => $request->get('is_active_in_course')
                    ]);
                }
                return BaseResponse::customResponse(
                    'Update successfully',
                    $lesson,
                    true,
                    200,
                    202,
                    'Accepted'
                );
            } else {
                return BaseResponse::customResponse(
                    'Fail to update',
                    [],
                    false,
                    Config('error_constant.lesson.insert_fail'),
                    422,
                    'Unprocessable Entity'
                );
            }
        } else {
            return BaseResponse::customResponse(
                'Lesson not found',
                [],
                false,
                Config('error_constant.vocabulary.not_found'),
                404,
                'NotFound'
            );
        }

    }

    /**
     * Update lesson in course
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCourseLesson(Request $request)
    {
        $id = $request->route('course_id');
        $course_lesson = CourseLesson::query()->where('course_id', $id)->where('lesson_id', $request->get('lesson_id'))->first();
        if ($course_lesson) {
            $data_change = $request->except('_method');
            $course_lesson->update($data_change);
            if ($course_lesson->save()) {
                return BaseResponse::customResponse(
                    'Update successfully',
                    $course_lesson,
                    true,
                    200,
                    202,
                    'Accepted'
                );
            } else {
                return BaseResponse::customResponse(
                    'Fail to update',
                    [],
                    false,
                    Config('error_constant.lesson.insert_fail'),
                    422,
                    'Unprocessable Entity'
                );
            }
        } else {
            return BaseResponse::customResponse(
                'Lesson not found',
                [],
                false,
                Config('error_constant.vocabulary.not_found'),
                404,
                'NotFound'
            );
        }
    }


    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLessonDetail(Request $request, $id)
    {
        $l = [];
        $course_lesson = null;
        $lesson = Lesson::query()->where('id', $id)->with('chapters')->first();
        if ($request->query('course_id')) {
            $course_lesson = CourseLesson::query()->where('course_id', $request->query('course_id'))->where('lesson_id', $id)->first();
        }
        if ($lesson) {
            $l['id'] = $lesson->id;
            $l['avatar'] = media_url_web( $lesson->avatar);
            $l['name'] = $lesson->name;
            $l['translations'] = $lesson->getTranslationsArray();
            $l['level_id'] = $lesson->level_id;
            $l['is_active'] = $lesson->is_active;
            $l['is_active_in_course'] = $course_lesson ? $course_lesson->is_active : null;
            $l['type'] = $lesson->type;
            $l['created_at'] = Carbon::parse($lesson->created_at)->format('d-m-Y');
            $l['chapters'] = $lesson->chapters;
            return BaseResponse::customResponse(
                'Get lesson success',
                $l,
                true,
                200,
                200,
                'Success'
            );
        } else {
            return BaseResponse::customResponse(
                'Lesson not found',
                [],
                false,
                Config('error_constant.vocabulary.not_found'),
                404,
                'NotFound'
            );
        }
    }

    /**
     * Update multiple sort order lesson in course.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSortInCourse(Request $request)
    {
        $sort = $request->all();
        $course_id = $request->route('course_id');
        foreach ($sort as $s) {
            CourseLesson::query()->where('course_id', $course_id)->where('lesson_id', $s['lesson_id'])->update(['sort_order' => $s['sort_order']]);
        }
        return BaseResponse::customResponse(
            'Update success',
            [],
            true,
            200,
            200,
            'Success'
        );
    }
}
