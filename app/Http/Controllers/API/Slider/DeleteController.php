<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:59
 */


namespace App\Http\Controllers\API\Slider;


use App\Http\Controllers\Controller;
use App\Models\Slider;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    public function delete(Request $request)
    {
        $list_id = $request->list_id;
        if (isset($list_id)) {
            foreach ($list_id as $id) {
                $slider = Slider::find($id);
                if ($slider != null) {
                    $image = $slider->image;
                    if ($slider->delete()) {
                        if ($image != null) {
                            UploadService::handleRemoveFile($image);
                        }
                    }
                }
            }
            return BaseResponse::customResponse(
                'Data is deleted',
                [],
                true,
                200,
                202,
                'Accepted'
            );
        } else {
            return BaseResponse::customResponse(
                'Fail to delete',
                [],
                false,
                Config('error_constant.slider.delete_fail'),
                422,
                'Unprocessable Entity'
            );

        }

    }
}
