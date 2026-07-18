<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 03/12/2018
 * Time: 10:28
 */

namespace App\Http\Controllers\API\Alphabet;


use App\Http\Controllers\Controller;
use App\Http\Requests\AlphabetRequest;
use App\Models\Alphabet;
use App\Models\Vocabulary;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{

    /**
     * Add new vocabulary.
     * @param AlphabetRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(AlphabetRequest $request)
    {
        $data = $request->all();
        $data['type'] = Alphabet::HIRAGANA;

        //check audio file
        if ($request->hasFile('audio')) {
            $audio = UploadService::handleUploadFile($request->file('audio'), Config('uploadpath.alphabet_audio_folder'));
            $data['audio'] = $audio;
        }

        //check image file
        if ($request->hasFile('image')) {
            $image = UploadService::handleUploadFile($request->file('image'), Config('uploadpath.alphabet_image_folder'));
            $data['image'] = $image;
        }


        $data['created_by'] = Auth::user()->id;
        $alphabet = Alphabet::query()->create($data);

        // get translation keys and add to translation table
        $language_keys = array_keys($data['translations']);
        foreach ($language_keys as $language) {
            $alphabet->translateOrNew($language)->meaning = $data['translations'][$language]['meaning'];
        }
        if ($alphabet->save()) {
            return BaseResponse::customResponse(
                'Create successfully',
                $alphabet,
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
                Config('error_constant.alphabet.insert_fail'),
                422,
                'Unprocessable Entity'
            );
        }
    }
}
