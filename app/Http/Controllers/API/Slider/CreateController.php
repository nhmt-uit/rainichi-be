<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Slider;


use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->all();
        //check image
        if ($request->hasFile('image')) {
            $image = UploadService::handleUploadFile($request->file('image'), Config('uploadpath.slider_image_folder'));
            $data['image'] = $image;
        }
        $data['created_by'] = Auth::user()->id;
        $slider = Slider::create($data);

        if($request->has('translations')) {
            $language_keys = array_keys($data['translations']);
            foreach ($language_keys as $language) {
                $slider->translateOrNew($language)->title = $data['translations'][$language]['title'];
                $slider->translateOrNew($language)->content = $data['translations'][$language]['content'];
            }
            if ($slider->save()) {
                return BaseResponse::customResponse(
                    'Create successfully',
                    $slider,
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
                    Config('error_constant.slider.insert_fail'),
                    422,
                    'Unprocessable Entity'
                );
            }
        }
    }
}
