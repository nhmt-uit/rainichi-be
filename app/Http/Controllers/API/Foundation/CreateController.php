<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Foundation;


use App\Http\Controllers\Controller;
use App\Models\Alphabet;
use App\Models\Foundation;
use App\Models\Number;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{
    /**
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createNumber(Request $request)
    {
        $number_data = $request->all();

        if ($request->hasFile('audio')) {
            $audio = UploadService::handleUploadFile($request->file('audio'), 'numbers/audio');
            $number_data['audio'] = $audio;
        }
        $number_data['created_by'] = Auth::user()->id;
        $number = Number::create($number_data);

        return BaseResponse::customResponse(
            'Create successful',
            $number,
            true,
            200,
            201,
            'Created'
        );
    }

    /**
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAlphabet(Request $request)
    {
        $alphabet_data = $request->all();
        if ($request->hasFile('image')) {
            $image = UploadService::handleUploadFile($request->file('image'), 'alphabet/images');
            $alphabet_data['image'] = $image;
        }
        if ($request->hasFile('audio')) {
            $audio = UploadService::handleUploadFile($request->file('audio'), 'alphabet/audio');
            $alphabet_data['audio'] = $audio;
        }
        $alphabet_data['created_by'] = Auth::user()->id;
        $alphabet = Alphabet::create($alphabet_data);

        return BaseResponse::customResponse(
            'Create successful',
            $alphabet,
            true,
            200,
            201,
            'Created'
        );

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createDefinition(Request $request)
    {
        $definition_data = $request->all();
        $definition_data['created_by'] = Auth::user()->id;
        $definition = Foundation::create($definition_data);
        $language_keys = array_keys($definition_data['translations']);
        foreach ($language_keys as $language) {
            $definition->translateOrNew($language)->definition = $definition_data['translations'][$language]['definition'];
            $definition->translateOrNew($language)->write = $definition_data['translations'][$language]['write'];
            $definition->translateOrNew($language)->read = $definition_data['translations'][$language]['read'];
        }
        if ($definition->save()) {
            return BaseResponse::customResponse(
                'Create successful',
                $definition,
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
