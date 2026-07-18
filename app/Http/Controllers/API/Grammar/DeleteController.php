<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Grammar;


use App\Http\Controllers\Controller;
use App\Models\Grammar;
use App\Models\GrammarGroup;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;
use PHPUnit\Framework\ExpectationFailedException;

class DeleteController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete(Request $request)
    {
        $list_id = $request->list_id;
        if (isset($list_id)) {
            foreach ($list_id as $id) {
                $vocabulary = Grammar::find($id);
                if ($vocabulary != null) {
                    $audio = $vocabulary->audio;
                    $video = $vocabulary->video;
                    $image = $vocabulary->image;
                    if ($vocabulary->delete()) {
                        if ($audio != null) {
                            UploadService::handleRemoveFile($audio);
                        }
                        if ($image != null) {
                            UploadService::handleRemoveFile($image);
                        }
                        if ($image != null) {
                            UploadService::handleRemoveFile($video);
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
        $grammar_id = $request->get('list_id');
        if (isset($grammar_id)) {
            foreach ($grammar_id as $id) {
                $group_chapter = GrammarGroup::query()->where('grammar_id', $id)->where('group_chapter_id', $group_chapter_id)->first();
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
