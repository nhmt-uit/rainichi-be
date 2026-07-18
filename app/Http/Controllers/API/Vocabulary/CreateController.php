<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 03/12/2018
 * Time: 10:28
 */

namespace App\Http\Controllers\API\Vocabulary;


use App\Http\Controllers\Controller;
use App\Http\Requests\VocabularyRequest;
use App\Models\Level;
use App\Models\Vocabulary;
use App\Models\VocabularyGroup;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{

    /**
     * Add new vocabulary.
     * @param VocabularyRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(VocabularyRequest $request)
    {
        $vocabulary_data = $request->all();
        //check audio file
        if ($request->hasFile('audio')) {

            $audio = UploadService::handleUploadFile($request->file('audio'), Config('uploadpath.vocabulary_audio_folder'));
            $vocabulary_data['audio'] = $audio;
        }
        //check image
        if ($request->hasFile('image')) {

            $image = UploadService::handleUploadFile($request->file('image'), Config('uploadpath.vocabulary_image_folder'));
            $vocabulary_data['image'] = $image;
        }

        $vocabulary_data['created_by'] = Auth::user()->id;
        if ($vocabulary_data['level_id'] == 0) {
            $vocabulary_data['level_id'] = null;
        }
        $vocabulary = Vocabulary::create($vocabulary_data);

        // get translation keys and add to translation table
        $language_keys = array_keys($vocabulary_data['translations']);
        foreach ($language_keys as $language) {
            $vocabulary->translateOrNew($language)->meaning = $vocabulary_data['translations'][$language]['meaning'];
            $vocabulary->translateOrNew($language)->chinese_vietnamese_word = $vocabulary_data['translations'][$language]['chinese_vietnamese_word'];
            $vocabulary->translateOrNew($language)->example1 = $vocabulary_data['translations'][$language]['example1'];
            $vocabulary->translateOrNew($language)->example2 = $vocabulary_data['translations'][$language]['example2'];
            if (array_key_exists('audio_example_1', $vocabulary_data['translations'][$language])) {
                if ($vocabulary_data['translations'][$language]['audio_example_1']) {
                    $audio_example_1 = UploadService::handleUploadFile($vocabulary_data['translations'][$language]['audio_example_1'], Config('uploadpath.vocabulary_audio_example_folder'));
                    $vocabulary->translateOrNew($language)->audio_example_1 = $audio_example_1;
                }

            }
            if (array_key_exists('audio_example_2', $vocabulary_data['translations'][$language])) {
                if ($vocabulary_data['translations'][$language]['audio_example_2']) {
                    $audio_example_2 = UploadService::handleUploadFile($vocabulary_data['translations'][$language]['audio_example_2'], Config('uploadpath.vocabulary_audio_example_folder'));
                    $vocabulary->translateOrNew($language)->audio_example_2 = $audio_example_2;
                }

            }
        }
        if ($vocabulary->save()) {
            if ($request->has('group_chapter_id')) {
                $vocabulary->groupVocabulary()->sync([$request->get('group_chapter_id')]);
            }
            return BaseResponse::customResponse(
                'Create successfully',
                $vocabulary,
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
}
