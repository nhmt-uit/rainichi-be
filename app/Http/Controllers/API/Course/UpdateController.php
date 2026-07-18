<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 14:00
 */

namespace App\Http\Controllers\API\Course;


use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CoursePromotion;
use App\Models\CourseRoute;
use App\Models\Promotion;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {

        $course = Course::find($request->route('id'));
        if ($course) {
            $course_data = $request->all();
            if ($request->hasFile('avatar')) {
                $old_avatar = $course->avatar;
                $avatar = UploadService::handleUploadFile($request->file('avatar'), Config('uploadpath.course_image_folder'));
                $course_data['avatar'] = $avatar;
                if ($old_avatar != null) {
                    UploadService::handleRemoveFile($old_avatar);
                }
            }
            if ($request->hasFile('youtube_link')) {
                $old_video = $course->youtube_link;
                $video = UploadService::handleUploadFile($request->file('youtube_link'), Config('uploadpath.course_video_folder'));
                $course_data['youtube_link'] = $video;
                if ($old_video != null) {
                    UploadService::handleRemoveFile($old_video);
                }
            }
            $course_data['updated_by'] = Auth::user()->id;
            $course->update($course_data);

            // get translation keys and add to translation table
            if ($request->translations) {
                $language_keys = array_keys($course_data['translations']);
                foreach ($language_keys as $language) {
                    $course->translateOrNew($language)->name = $course_data['translations'][$language]['name'];
                    $course->translateOrNew($language)->slug = str_slug($course_data['translations'][$language]['name']);
                    $course->translateOrNew($language)->description = $course_data['translations'][$language]['description'];
                    $course->translateOrNew($language)->landing_description = $course_data['translations'][$language]['landing_description'] ?? null;
                    $course->translateOrNew($language)->landing_gift = $course_data['translations'][$language]['landing_gift'] ?? null;
                }
            }
            if ($course->save()) {
                if ($request->prices) {
                    if ($course_data['prices']) {
                        $course->prices()->delete();
                        $course->prices()->createMany($course_data['prices']);
                    }
                }
                if (array_key_exists('course_routes', $course_data)) {
                    foreach ($course_data['course_routes'] as $course_route) {
                        self::updateCourseRoute($course->id, $course_route);
                    }
                }
                #add promotion for course
                if (array_key_exists('promotion_id', $course_data)) {
                    $promotion = Promotion::query()->find($course_data['promotion_id']);
                    $course->promotion()->sync($promotion);
                }
                return BaseResponse::customResponse(
                    'Update successfully',
                    $course,
                    true,
                    200,
                    202,
                    'Accepted'
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
     * @param $course_id
     * @param $data
     */
    static function updateCourseRoute($course_id, $data)
    {
        if (array_key_exists('id', $data)) {
            $course_route = CourseRoute::query()->find($data['id']);
            if ($course_route) {
                if (array_key_exists('durations', $data)) {
                    $course_route->update($data);
                }
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
                }

                $course_route->save();
            }
        } else {
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

}
