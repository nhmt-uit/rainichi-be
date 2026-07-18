<?php

namespace App\Http\Controllers\API\UserApply;

use App\Models\Article;
use App\Models\UserApply;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeleteController extends Controller
{
    /**
     * Delete vocabulary by id.
     * @param Request $request
     * @return JsonResponse
     */
    public function deleteApply(Request $request)
    {
        $list_id = $request->list_id;
        if (isset($list_id)) {
            foreach ($list_id as $id) {
                $user_apply = UserApply::find($id);
                $user_apply->delete();
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
