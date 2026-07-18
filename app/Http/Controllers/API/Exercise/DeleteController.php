<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:59
 */

namespace App\Http\Controllers\API\Exercise;


use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionGroup;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    public function delete(Request $request)
    {
        $list_id = $request->list_id;
        if (isset($list_id)) {
            foreach ($list_id as $id) {
                $question = Question::find($id);
                if ($question != null) {
                    $media = $question->media;
                    $image = $question->image;
                    if ($question->delete()) {
                        if ($media != null) {
                            UploadService::handleRemoveFile($media);
                        }
                        if ($image != null) {
                            UploadService::handleRemoveFile($image);
                        }
                    }
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
    public function deleteInGroup(Request $request)
    {
        $group_chapter_id = $request->route('group_id');
        $question_id = $request->get('list_id');
        if (isset($question_id)) {
            foreach ($question_id as $id) {
                $group_chapter = QuestionGroup::query()->where('question_id', $id)->where('group_chapter_id', $group_chapter_id)->first();
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
