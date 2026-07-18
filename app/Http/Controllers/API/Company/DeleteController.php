<?php

namespace App\Http\Controllers\API\Company;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Support\Facades\Auth;

class DeleteController extends Controller
{
    /**
     * Delete company by id.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $list_id = $request->list_id;
        if (isset($list_id) && Auth::user()->type === User::ADMIN) {
            foreach ($list_id as $id) {
                $company = Company::query()->find($id);
                if ($company) {
                    $company->forceDelete();
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
