<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 03/12/2018
 * Time: 11:50
 */

namespace App\Http\Controllers\API\GroupChapter;


use App\Http\Controllers\Controller;
use App\Models\GroupChapter;
use App\Models\LessonVocabulary;
use App\Models\VocabularyGroup;
use App\Service\BaseResponse;
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
                $group_chapter = GroupChapter::find($id);
                if ($group_chapter != null) {
                    $group_chapter->delete();
                }
            }
            return BaseResponse::customResponse(
                'Deleted',
                $group_chapter,
                true,
                200,
                202,
                'Accepted'
            );
        } else {
            return BaseResponse::customResponse(
                'Fail to delete',
                [],
                false,
                Config('error_constant.vocabulary.delete_fail'),
                422,
                'Unprocessable Entity'
            );

        }


    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function deleteVocabulary(Request $request)
    {
        $group_chapter_id = $request->route('id');
        $vocabulary_id = $request->get('list_id');
        if (isset($vocabulary_id)) {
            foreach ($vocabulary_id as $id) {
                $group_chapter = VocabularyGroup::query()->where('vocabulary_id', $id)->where('group_chapter_id', $group_chapter_id)->first();
                if ($group_chapter != null) {
                    $group_chapter->delete();
                }
            }
            return BaseResponse::customResponse(
                'Deleted',
                [],
                true,
                200,
                202,
                'Accepted'
            );
        } else {
            return BaseResponse::customResponse(
                'Fail to delete',
                [],
                false,
                Config('error_constant.vocabulary.delete_fail'),
                422,
                'Unprocessable Entity'
            );
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function deleteInLesson(Request $request)
    {
        $lesson_id = $request->route('lesson_id');
        $group_chapter_id = $request->get('group_chapter_id');
        if (isset($group_chapter_id)) {

            foreach ($group_chapter_id as $id) {
                $group_chapter = LessonVocabulary::query()->where('lesson_id', $lesson_id)->where('group_chapter_id', $id)->first();
                if ($group_chapter != null) {
                    $group_chapter->delete();
                }
            }
            return BaseResponse::customResponse(
                'Deleted',
                [],
                true,
                200,
                202,
                'Accepted'
            );
        } else {
            return BaseResponse::customResponse(
                'Fail to delete',
                [],
                false,
                Config('error_constant.vocabulary.delete_fail'),
                422,
                'Unprocessable Entity'
            );
        }
    }
}
