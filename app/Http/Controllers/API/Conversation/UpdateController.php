<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 14:00
 */

namespace App\Http\Controllers\API\Conversation;


use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Service\BaseResponse;
use App\Service\UploadService;
use App\Transformers\ConversationTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class UpdateController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var VocabularyAdminTransformer
     */
    private $conversationTransformer;

    function __construct(Manager $fractal, ConversationTransformer $conversationTransformer)
    {
        $this->fractal = $fractal;
        $this->conversationTransformer = $conversationTransformer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $conversation_id = $request->route('id');
        $data_change = $request->except('_method');
        if (array_key_exists('level_id', $data_change)) {
            if ($data_change['level_id'] == 0) $data_change['level_id'] = null;
        }
        $conversation = Conversation::find($conversation_id);
        if ($conversation) {
            if ($request->translations) {
                $language_keys = array_keys($data_change['translations']);
                foreach ($language_keys as $language) {
                    $conversation->translateOrNew($language)->name = $data_change['translations'][$language]['name'];
                    if (array_key_exists('sub_title', $data_change['translations'][$language])) {
                        if ($data_change['translations'][$language]['sub_title']) {
                            $sub_title = UploadService::handleUploadFile($data_change['translations'][$language]['sub_title'], Config('uploadpath.conversation_sub_title_folder'));
                            $conversation->translateOrNew($language)->sub_title = $sub_title;
                        }

                    }

                }
            }
            if ($request->hasFile('media')) {
                Log::info('has file');
                $old_audio = $conversation->audio;
                $media = UploadService::handleUploadFile($request->file('media'), Config('uploadpath.conversation_media_folder'));
                if (intval($request->type) === Conversation::AUDIO) {
                    $data_change['audio'] = $media;
                } else if (intval($request->type) === Conversation::VIDEO) {
                    $data_change['video'] = $media;
                }
                if ($old_audio != null) {
                    UploadService::handleRemoveFile($old_audio);
                }
            } else {
                Log::info('No file');
            }
            //check vidio
            if ($request->hasFile('image')) {
                $old_image = $conversation->image;
                $image = UploadService::handleUploadFile($request->file('image'), Config('uploadpath.conversation_image_folder'));
                $data_change['image'] = $image;
                if ($old_image != null) {
                    UploadService::handleRemoveFile($old_image);
                }
            }
            $data_change['updated_by'] = Auth::user()->id;
            $conversation->update($data_change);
            if ($conversation->save()) {
                return BaseResponse::customResponse(
                    'Update successfully',
                    $conversation,
                    true,
                    200,
                    200,
                    'Success'
                );
            }
        } else {
            return BaseResponse::customResponse(
                'Vocabulary not found',
                [],
                false,
                Config('error_constant.vocabulary.not_found'),
                404,
                'NotFound'
            );
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail($id)
    {
        $conversation = Conversation::query()->find($id);
        if ($conversation) {
            $conversation = new Item($conversation, $this->conversationTransformer);
            $conversation = $this->fractal->createData($conversation);
            return BaseResponse::customResponse(
                'Update successfully',
                $conversation->toArray()['data'],
                true,
                200,
                200,
                'Success'
            );

        } else {
            return BaseResponse::customResponse(
                'Vocabulary not found',
                [],
                false,
                Config('error_constant.vocabulary.not_found'),
                404,
                'NotFound'
            );
        }
    }

}
