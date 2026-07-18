<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:59
 */

namespace App\Http\Controllers\API\User;


use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use App\Service\BaseResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeleteController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        if (Auth::user()->type == User::ADMIN) {
            $list_user = $request->list_user_id;

            User::query()->whereIn('id', $list_user)->forceDelete();
            SocialAccount::query()->whereIn('user_id', $list_user)->delete();
            return BaseResponse::customResponse(
                'Successful delete',
                [],
                true,
                200,
                200,
                "Deleted",
                []
            );
        } else {
            return BaseResponse::customResponse(
                'Permission denied',
                [],
                false,
                403,
                403,
                "Permission denied",
                []
            );
        }
    }
}
