<?php
/**
 * Created by PhpStorm.
 * User: thachnguyen
 */

namespace App\Http\Controllers\API\Slider;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Slider;
use App\Service\BaseResponse;
use App\Service\UploadService;
use App\Transformers\SliderTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class UpdateController extends Controller
{
    private $fractal;
    /**
     * @var SliderTransformer
     */
    private $sliderTransformer;

    function __construct(Manager $fractal, SliderTransformer $sliderTransformer) {
        $this->fractal = $fractal;
        $this->sliderTransformer = $sliderTransformer;
    }
    public function update(Request $request)
    {
        if ($request->isMethod('patch')) {
            $slider_id = $request->route('id');
            $data_change = $request->except('_method');
            $data_change = $request->all();
            $slider = Slider::find($slider_id);
            if($slider){
                if ($request->translations) {
                    $language_keys = array_keys($data_change['translations']);
                    foreach ($language_keys as $language) {
                        $slider->translateOrNew($language)->title = $data_change['translations'][$language]['title'];
                        $slider->translateOrNew($language)->content = $data_change['translations'][$language]['content'];
                    }
                }

                //check image
                if ($request->hasFile('image')) {
                    $old_image = $slider->image;
                    $image = UploadService::handleUploadFile($request->file('image'), Config('uploadpath.slider_image_folder'));
                    $data_change['image'] = $image;
                    if ($old_image != null) {
                        UploadService::handleRemoveFile($old_image);
                    }
                }

                $data_change['updated_by'] = Auth::user()->id;
                $slider->update($data_change);

                if ($slider->save()) {
                    return BaseResponse::customResponse(
                        'Slider updated successfully',
                        $slider,
                        true,
                        200,
                        200,
                        'Success'
                    );
                }
            }
            else
            {
                return BaseResponse::customResponse(
                    'Data not found',
                    [],
                    false,
                    Config('error_constant.slider.not_found'),
                    404,
                    'NotFound'
                );
            }
        }
    }

    public function detail(Request $request){
        $slider_id = $request->route('id');
        $slider = Slider::query()->find($slider_id);
        if($slider){
            $slider = new Item($slider, $this->sliderTransformer);
            $slider = $this->fractal->createData($slider);
            return BaseResponse::customResponse(
                'Get data successfully',
                $slider->toArray()['data'],
                true,
                200,
                200,
                'Success'
            );
        }
        else {
            return BaseResponse::customResponse(
                'Data not found',
                [],
                false,
                Config('error_constant.article.not_found'),
                404,
                'NotFound'
            );
        }
    }

}
