<?php

namespace App\Http\Controllers\API\Company\User;

use App\Models\Company;
use App\Models\User;
use App\Service\BaseResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DeleteController extends Controller
{
    /**
     * Delete User company
     * @params Request $request
     * @param Request $request
     * @return  JsonResponse
     */

    public function deleteCompanyUser(Request $request)
    {
        $company_id = $request->route('id');
        $user_ids = $request->list_id;
        if (isset($user_ids) && Auth::user()->type === User::ADMIN) {
            $company = Company::query()->find($company_id);
            $company->users()->detach($user_ids);
        }
        return BaseResponse::customResponse(
            'Deleted company user',
            [],
            true,
            200,
            202,
            'Accepted'
        );
    }
}
