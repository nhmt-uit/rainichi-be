<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 03/12/2018
 * Time: 11:50
 */

namespace App\Http\Controllers\API\Test;


use App\Http\Controllers\Controller;
use App\Models\CourseTest;
use App\Models\QuestionGroup;
use App\Models\Test;
use App\Models\TestChapter;
use App\Models\TestQuestions;
use App\Models\TestTimes;
use App\Models\Vocabulary;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    /**
     * Delete test by id.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $list_id = $request->list_id;
        $excep_ids = [];
        if (isset($list_id)) {
            foreach ($list_id as $id) {
                $test = Test::find($id);
                if ($test != null && $test->orders->count() <= 0) {
                    $video = $test->video;
                    if ($test->delete()) {
                        if ($video != null) {
                            UploadService::handleRemoveFile($video);
                        }
                    }
                }else{
                    array_push( $excep_ids, $id);
                }
            }
            if(sizeof($excep_ids))
            {
                return BaseResponse::customResponse(
                    'Some id can\'t deleted. Because of their not exist or has orders',
                    $excep_ids,
                    true,
                    409,
                    409 ,
                    'Unprocessable'
                );
            }else
            {
                return BaseResponse::customResponse(
                    'Deleted',
                    [],
                    true,
                    200,
                    202,
                    'Accepted'
                );
            }

        } else {
            return BaseResponse::customResponse(
                'Fail to delete',
                [],
                false,
                Config('error_constant.test.delete_fail'),
                422,
                'Unprocessable Entity'
            );

        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteQuestionGroupInTest(Request $request)
    {
        $chapter_id = $request->get('chapter_id');
        $group_id = $request->get('group_id');
        $test_time_id = $request->route('test_time_id');

        $testTime = TestTimes::query()->find($test_time_id);
        $test_chapter = TestChapter::query()->where('chapter_id', $chapter_id)
                                            ->where('test_id', $testTime->test_id)->first();

        foreach ($group_id as $g) {
            TestQuestions::query()->where('test_time_id', $test_time_id)
                                  ->where('test_chapter_id', $test_chapter->id)
                                  ->where('group_chapter_id', $g)->delete();
        }
        return BaseResponse::customResponse(
            'Deleted',
            [],
            true,
            200,
            200,
            'Deleted'
        );

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
                $lesson = CourseTest::query()->where('test_id', $id)->where('course_id', $request->route('id'));
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
