<?php

namespace App\Http\Controllers\API\UserApply;

use App\Models\Category;
use App\Models\UserApply;
use App\Service\BaseResponse;
use App\Transformers\CategoryAdminTransformer;
use App\Transformers\UserApplyAdminTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UpdateController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $category_id = $request->route('id');
        $data_change = $request->except('_method');
        $user_apply = UserApply::find($category_id);
        if ($user_apply) {
            $data_change['updated_by'] = Auth::user()->id;
            $user_apply->update($data_change);
            return BaseResponse::customResponse(
                'Update user_apply successfully',
                (new UserApplyAdminTransformer)->transform($user_apply),
                true,
                200,
                200,
                'Success'
            );

        }
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
