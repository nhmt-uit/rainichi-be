<?php

namespace App\Http\Controllers\API\Promotion;

use App\Models\Promotion;
use App\Service\BaseResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeleteController extends Controller
{
    /**
     * @param Request $request
     * @return mixed
     */
    public function delete(Request $request)
    {
        $list_id = $request->list_id;
        if (isset($list_id)) {
            foreach ($list_id as $id) {
                $slider = Promotion::find($id);
                if ($slider != null) {
                    $slider->delete();
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
                Config('error_constant.normal.delete_fail'),
                422,
                'Unprocessable Entity'
            );

        }

    }
}
