<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 03/12/2018
 * Time: 11:50
 */

namespace App\Http\Controllers\API\Reading;


use App\Http\Controllers\Controller;
use App\Models\Reading;
use App\Models\ReadingGroup;
use App\Service\BaseResponse;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    /**
     * Delete reading by id.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $list_id = $request->list_id;
        if (isset($list_id)) {
            foreach ($list_id as $id) {
                $reading = Reading::find($id);
                if ($reading != null) {
                    $reading->delete();
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
                Config('error_constant.reading.delete_fail'),
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
                $group_chapter = ReadingGroup::query()->where('reading_id', $id)->where('group_chapter_id', $group_chapter_id)->first();
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
