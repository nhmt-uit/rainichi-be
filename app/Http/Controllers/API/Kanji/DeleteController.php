<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:59
 */

namespace App\Http\Controllers\API\Kanji;


use App\Http\Controllers\Controller;
use App\Models\Kanji;
use App\Models\KanjiGroup;
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
                $kanji = Kanji::find($id);
                if ($kanji != null) {
                    $audio = $kanji->audio;
                    $image = $kanji->image;
                    if ($kanji->delete()) {
                        if ($audio != null) {
                            UploadService::handleRemoveFile($audio);
                        }
                        if ($image != null) {
                            UploadService::handleRemoveFile($image);
                        }

                    }
                }
            }
            return BaseResponse::customResponse(
                'Deleted',
                $kanji,
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
        $kanji_id = $request->get('list_id');
        if (isset($kanji_id)) {
            foreach ($kanji_id as $id) {
                $group_chapter = KanjiGroup::query()->where('kanji_id', $id)->where('group_chapter_id', $group_chapter_id)->first();
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
