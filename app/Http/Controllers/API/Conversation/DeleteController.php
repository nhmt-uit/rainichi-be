<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:59
 */

namespace App\Http\Controllers\API\Conversation;


use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ConversationGroup;
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
                $conversation = Conversation::find($id);
                if ($conversation != null) {
                    $media = null;
                    if ($conversation->type === Conversation::AUDIO) {
                        $media = $conversation->audio;
                    } else if ($conversation->type === Conversation::VIDEO) {
                        $media = $conversation->video;
                    }

                    $image = $conversation->image;
                    if ($conversation->delete()) {
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
        $conversation_id = $request->get('list_id');
        if (isset($conversation_id)) {
            foreach ($conversation_id as $id) {
                $group_chapter = ConversationGroup::query()->where('conversation_id', $id)->where('group_chapter_id', $group_chapter_id)->first();
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
