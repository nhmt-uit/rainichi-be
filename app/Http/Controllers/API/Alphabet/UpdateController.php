<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Alphabet;


use App\Http\Controllers\Controller;
use App\Models\Alphabet;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateController extends Controller
{
    /**
     * Update alphabet
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $id = $request->route('id');
        $type = $request->route('type');
        $data = $request->except('_method');
        $alphabet = Alphabet::query()
            ->where('id', $id)
            ->where('type', Alphabet::TYPES[$type])
            ->first();

        if ($alphabet) {
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

            if ($request->translations) {
                $language_keys = array_keys($data['translations']);
                foreach ($language_keys as $language) {
                    $alphabet->translateOrNew($language)->meaning = $data['translations'][$language]['meaning'];
                }
            }
            $data['updated_by'] = Auth::user()->id;

            $alphabet->update($data);
            if ($alphabet->save()) {
                $alphabet->audio = media_url_web( $alphabet->audio);
                $alphabet->image = media_url_web( $alphabet->image);

                return BaseResponse::customResponse(
                    'Update successfully',
                    $alphabet,
                    true,
                    200,
                    200,
                    'Success'
                );
            }
        } else {
            return BaseResponse::customResponse(
                $type . ' not found',
                [],
                false,
                Config('error_constant.alphabet.alphabet_not_found'),
                404,
                'NotFound'
            );
        }
    }

}
