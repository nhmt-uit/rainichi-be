<?php

namespace App\Http\Controllers\API\LandingPage;

use App\Models\Article;
use App\Models\LandingContent;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeleteController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        $list_id = $request->list_id;
        if (isset($list_id)) {
            foreach ($list_id as $id) {
                $content = LandingContent::find($id);
                if ($content != null) {
                    $image = $content->image;
                    if ($content->delete()) {
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
                Config('error_constant.normal.delete_fail'),
                422,
                'Unprocessable Entity'
            );

        }

    }
}
