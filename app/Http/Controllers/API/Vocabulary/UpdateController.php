<?php

namespace App\Http\Controllers\API\Vocabulary;

use App\Http\Controllers\Controller;
use App\Models\Vocabulary;
use App\Models\VocabularyTranslations;
use App\Service\BaseResponse;
use App\Service\UploadService;
use App\Transformers\VocabularyAdminTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    private $vocabularyTransformer;

    function __construct(Manager $fractal, VocabularyAdminTransformer $vocabularyTransformer)
    {
        $this->fractal = $fractal;
        $this->vocabularyTransformer = $vocabularyTransformer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $vocabulary_id = $request->route('id');
        $data_change = $request->except('_method');
        if ($request->get('level_id') == 0) {
            $data_change['level_id'] = null;
        }
        $vocabulary = Vocabulary::find($vocabulary_id);
        if ($vocabulary) {
            if ($request->translations) {
                $language_keys = array_keys($data_change['translations']);
                foreach ($language_keys as $language) {
                    $vocabulary->translateOrNew($language)->meaning = $data_change['translations'][$language]['meaning'];
                    $vocabulary->translateOrNew($language)->chinese_vietnamese_word = $data_change['translations'][$language]['chinese_vietnamese_word'];
                    $vocabulary->translateOrNew($language)->example1 = $data_change['translations'][$language]['example1'];
                    $vocabulary->translateOrNew($language)->example2 = $data_change['translations'][$language]['example2'];
                    if (array_key_exists('audio_example_1', $data_change['translations'][$language])) {
                        if ($data_change['translations'][$language]['audio_example_1'] && $request->hasFile('translations.' . $language . '.audio_example_1')) {
                            $audio_example_1 = UploadService::handleUploadFile($data_change['translations'][$language]['audio_example_1'], Config('uploadpath.vocabulary_audio_example_folder'));
                            $vocabulary->translateOrNew($language)->audio_example_1 = $audio_example_1;
                        } else if ($data_change['translations'][$language]['audio_example_1'] === "0") {
                            $vocabulary->translateOrNew($language)->audio_example_1 = null;
                        }

                    }
                    if (array_key_exists('audio_example_2', $data_change['translations'][$language])) {
                        if ($data_change['translations'][$language]['audio_example_2'] && $request->hasFile('translations.' . $language . '.audio_example_2')) {
                            $audio_example_2 = UploadService::handleUploadFile($data_change['translations'][$language]['audio_example_2'], Config('uploadpath.vocabulary_audio_example_folder'));
                            $vocabulary->translateOrNew($language)->audio_example_2 = $audio_example_2;
                        } else if ($data_change['translations'][$language]['audio_example_2'] === "0") {
                            $vocabulary->translateOrNew($language)->audio_example_2 = null;
                        }

                    }
                }
            }
            if ($request->hasFile('audio')) {
                $old_audio = $vocabulary->audio;
                $audio = UploadService::handleUploadFile($request->file('audio'), Config('uploadpath.vocabulary_audio_folder'));
                $data_change['audio'] = $audio;
                if ($old_audio != null) {
                    UploadService::handleRemoveFile($old_audio);
                }
            }
            //check image
            if ($request->hasFile('image')) {
                $old_image = $vocabulary->image;
                $image = UploadService::handleUploadFile($request->file('image'), Config('uploadpath.vocabulary_image_folder'));
                $data_change['image'] = $image;
                if ($old_image != null) {
                    UploadService::handleRemoveFile($old_image);
                }
            }
            $data_change['updated_by'] = Auth::user()->id;
            $vocabulary->update($data_change);
            if ($vocabulary->save()) {
                return BaseResponse::customResponse(
                    'Update successfully',
                    $vocabulary,
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

    public function detail($id)
    {
        $vocabulary = Vocabulary::query()->find($id);
        if ($vocabulary) {
            $vocabulary = new Item($vocabulary, $this->vocabularyTransformer);
            $vocabulary = $this->fractal->createData($vocabulary);
            return BaseResponse::customResponse(
                'Update successfully',
                $vocabulary->toArray()['data'],
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

//    public function updateAudio()
//    {
//        $vt = VocabularyTranslations::query()->where('locale', 'vi')->where('audio_example_1', '<>', null)->get();
//        foreach ($vt as $i) {
//            VocabularyTranslations::query()->where('vocabulary_id', $i->vocabulary_id)
//                ->where('locale', 'en')
//            ->update([
//                'audio_example_1' => $i->audio_example_1
//            ]);
//        }
//        return [
//            'message' => 'Success'
//        ];
//    }
}
