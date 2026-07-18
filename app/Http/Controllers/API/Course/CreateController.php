<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Course;


use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\CoursePromotion;
use App\Models\CourseRoute;
use App\Models\CourseTest;
use App\Models\Promotion;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{
    /**
     * @param CourseRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(CourseRequest $request)
    {

        $course_data = $request->all();
        if ($request->hasFile('avatar')) {

            $avatar = UploadService::handleUploadFile($request->file('avatar'), Config('uploadpath.course_image_folder'));
            $course_data['avatar'] = $avatar;
        } else {
            $course_data['avatar'] = 'default.png';
        }
        if ($course_data['level_id'] == 0) {
            $course_data['level_id'] = null;
        }
        if ($request->hasFile('youtube_link')) {

            $video = UploadService::handleUploadFile($request->file('youtube_link'), Config('uploadpath.course_video_folder'));
            $course_data['youtube_link'] = $video;
        }
        $course_data['created_by'] = Auth::user()->id;

        $course = Course::create($course_data);

        // get translation keys and add to translation table
        $language_keys = array_keys($course_data['translations']);
        foreach ($language_keys as $language) {
            $course->translateOrNew($language)->name = $course_data['translations'][$language]['name'];
            $course->translateOrNew($language)->slug = str_slug($course_data['translations'][$language]['name']);
            $course->translateOrNew($language)->description = $course_data['translations'][$language]['description'];
            $course->translateOrNew($language)->landing_description = $course_data['translations'][$language]['landing_description'] ?? null;
            $course->translateOrNew($language)->landing_gift = $course_data['translations'][$language]['landing_gift'] ?? null;
        }

        if ($course->save()) {
            $course->prices()->createMany($course_data['prices']);
            if (array_key_exists('course_routes', $course_data)) {
                foreach ($course_data['course_routes'] as $course_route) {
                    self::addCourseRoute($course->id, $course_route);
                }
            }
            #add promotion for course
            if (array_key_exists('promotion_id', $course_data)) {
                $promotion = Promotion::query()->find($course_data['promotion_id']);
                $course->promotion()->attach($promotion);
            }
            return BaseResponse::customResponse(
                'Create successfully',
                $course,
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addLesson(Request $request)
    {
        $course_id = $request->route('id');
        $lesson_list = $request->lessons;
        foreach ($lesson_list as $lesson_id) {
            $cl = CourseLesson::query()->where('course_id', $course_id)->where('lesson_id', $lesson_id)->first();
            if (!$cl) {
                $course_lesson = new CourseLesson();
                $course_lesson->course_id = $course_id;
                $course_lesson->lesson_id = $lesson_id;
                $course_lesson->save();
            }
        }
        return BaseResponse::customResponse(
            'Add lesson success',
            [],
            true,
            200,
            201,
            'Created'
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addTest(Request $request)
    {
        $course_id = $request->route('id');
        $test_list = $request->tests;
        foreach ($test_list as $test_id) {
            $cl = CourseTest::query()->where('course_id', $course_id)->where('test_id', $test_id)->first();
            if (!$cl) {
                $course_test = new CourseTest();
                $course_test->course_id = $course_id;
                $course_test->test_id = $test_id;
                $course_test->save();
            }
        }
        return BaseResponse::customResponse(
            'Add test success',
            [],
            true,
            200,
            201,
            'Created'
        );
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addLessonFollowings(Request $request)
    {
        $course_id = $request->route('id');
        $lesson_id = $request->get('lesson_id');
        $follow_lesson_ids = $request->get('follow_lesson_ids');

        $validator = \Validator::make($request->all(), [
            'lesson_id' => 'required',
        ]);
        if ($validator->fails()) {
            return BaseResponse::customResponse(
                $validator->errors()->first(),
                $request->all(),
                false,
                Config('error_constant.lesson.invalid'),
                422,
                'Unprocessed Entity'
            );
        }
        CourseLesson::query()->where('course_id', $course_id)->where('lesson_id', $lesson_id)->update(['following_lesson' => $follow_lesson_ids]);

        return BaseResponse::customResponse(
            'Add following success',
            [],
            true,
            200,
            201,
            'Created'
        );
    }

    /**
     * @param $course_id
     * @param $data
     */
    static function addCourseRoute($course_id, $data)
    {
        $data['course_id'] = $course_id;
        $course_route = CourseRoute::query()->create($data);
        if (array_key_exists('translations', $data)) {
            $language_keys = array_keys($data['translations']);
            foreach ($language_keys as $language) {
                $image = null;
                if (array_key_exists('image', $data['translations'][$language])) {
                    if ($data['translations'][$language]['image']) {
                        $image = UploadService::handleUploadFile($data['translations'][$language]['image'], Config('uploadpath.course_routes_folder'));
                        $course_route->translateOrNew($language)->image = $image;
                    }

                }

            }
            $course_route->save();
        }
    }
}
