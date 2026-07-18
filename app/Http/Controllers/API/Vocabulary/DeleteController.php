<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 03/12/2018
 * Time: 11:50
 */

namespace App\Http\Controllers\API\Vocabulary;


use App\Http\Controllers\Controller;
use App\Models\Vocabulary;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    /**
     * Delete vocabulary by id.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $list_id = $request->list_id;
        if (isset($list_id)) {
            foreach ($list_id as $id) {
                $vocabulary = Vocabulary::find($id);
                if ($vocabulary != null) {
                    $audio = $vocabulary->audio;
                    $image = $vocabulary->image;
                    if ($vocabulary->delete()) {
                        if ($audio != null) {
                            UploadService::handleRemoveFile($audio);
                        }
                        if ($image != null) {
                            UploadService::handleRemoveFile($image);
                        }
                    }
                }
            }
            return BaseResponse::customResponse(
                'Deleted',
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
                Config('error_constant.vocabulary.delete_fail'),
                422,
                'Unprocessable Entity'
            );

        }

    }
}
