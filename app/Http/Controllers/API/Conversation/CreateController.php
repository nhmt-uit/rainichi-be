<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Conversation;


use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ConversationGroup;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $conversation_data = $request->all();
        //check audio file
        if ($request->hasFile('media')) {

            $media = UploadService::handleUploadFile($request->file('media'), Config('uploadpath.conversation_media_folder'));

            if (intval($request->type) === Conversation::AUDIO) {
                $conversation_data['audio'] = $media;
            } else if (intval($request->type) === Conversation::VIDEO) {
                $conversation_data['video'] = $media;
            }

        }
        //check vidio
        if ($request->hasFile('image')) {

            $image = UploadService::handleUploadFile($request->file('image'), Config('uploadpath.conversation_image_folder'));
            $conversation_data['image'] = $image;
        }
        $conversation_data['created_by'] = Auth::user()->id;
        if ($conversation_data['level_id'] == 0) {
            $conversation_data['level_id'] = null;
        }
        $conversation = Conversation::query()->create($conversation_data);
        // get translation keys and add to translation table
        $language_keys = array_keys($conversation_data['translations']);
        foreach ($language_keys as $language) {

            $conversation->translateOrNew($language)->name = $conversation_data['translations'][$language]['name'];
            $sub_title = null;
            if (array_key_exists('sub_title', $conversation_data['translations'][$language])) {
                if ($conversation_data['translations'][$language]['sub_title']) {
                    $sub_title = UploadService::handleUploadFile($conversation_data['translations'][$language]['sub_title'], Config('uploadpath.conversation_sub_title_folder'));
                    $conversation->translateOrNew($language)->sub_title = $sub_title;
                }

            }

        }
        if ($conversation->save()) {
            if ($request->has('group_chapter_id')) {
                $conversation->groupConversation()->sync([$request->get('group_chapter_id')]);
            }
            return BaseResponse::customResponse(
                'Create successfully',
                $conversation,
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
     * Add conversation to group
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToGroup(Request $request)
    {
        $conversation = $request->get('conversation_id');
        foreach ($conversation as $c) {
            $data = ConversationGroup::query()->where('conversation_id', $c)->where('group_chapter_id', $request->route('group_id'))->first();
            if (!$data) {
                ConversationGroup::query()->create([
                    'conversation_id' => $c,
                    'group_chapter_id' => $request->route('group_id')
                ]);
            }
        }
        return BaseResponse::customResponse(
            'Create successfully',
            [],
            true,
            200,
            201,
            'Created'
        );
    }
}
