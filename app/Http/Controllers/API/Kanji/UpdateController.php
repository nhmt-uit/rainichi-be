<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 14:00
 */

namespace App\Http\Controllers\API\Kanji;


use App\Http\Controllers\Controller;
use App\Models\Kanji;
use App\Service\BaseResponse;
use App\Service\UploadService;
use App\Transformers\KanjiAdminTransformer;
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
     * @var KanjiAdminTransformer
     */
    private $kanjiAdminTransformer;

    function __construct(Manager $fractal, KanjiAdminTransformer $kanjiAdminTransformer)
    {
        $this->fractal = $fractal;
        $this->kanjiAdminTransformer = $kanjiAdminTransformer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $vocabulary_id = $request->route('id');
        $data_change = $request->except('_method');
        $vocabulary = Kanji::find($vocabulary_id);
        if ($vocabulary) {
            $check = Kanji::query()->where('kanji', $data_change['kanji'])->first();
            if (($check && $check->id == $vocabulary_id) || !$check) {
                if ($request->translations) {
                    $language_keys = array_keys($data_change['translations']);
                    foreach ($language_keys as $language) {
                        $vocabulary->translateOrNew($language)->meaning = $data_change['translations'][$language]['meaning'];
                        $vocabulary->translateOrNew($language)->chinese_vietnamese_word = $data_change['translations'][$language]['chinese_vietnamese_word'];
                        $vocabulary->translateOrNew($language)->example1 = $data_change['translations'][$language]['example1'];
                        $vocabulary->translateOrNew($language)->example2 = $data_change['translations'][$language]['example2'];
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
                if ($data_change['level_id'] == 0) {
                    $data_change['level_id'] = null;
                }
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
                return response()->json([
                    "message" => "This kanji has already been taken",
                    "errors" => [
                        "kanji" => [
                            "validation.unique"
                        ]
                    ]
                ])->setStatusCode(422);
            }
        } else {
            return BaseResponse::customResponse(
                'Kanji not found',
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
        $vocabulary = Kanji::query()->find($id);
        if ($vocabulary) {
            $vocabulary = new Item($vocabulary, $this->kanjiAdminTransformer);
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
                'Kạnji not found',
                [],
                false,
                Config('error_constant.vocabulary.not_found'),
                404,
                'NotFound'
            );
        }
    }

    public function detailByKanji($kanji)
    {
        $vocabulary = Kanji::query()->where('kanji', $kanji)->first();
        if ($vocabulary) {
            $vocabulary = new Item($vocabulary, $this->kanjiAdminTransformer);
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
                'Kạnji not found',
                [],
                false,
                Config('error_constant.vocabulary.not_found'),
                404,
                'NotFound'
            );
        }
    }

}
