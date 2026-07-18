<?php

namespace App\Http\Controllers\API\Company\User;

use App\Models\Company;
use App\Models\User;
use App\Service\BaseResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CreateController extends Controller
{
    /**
     * Add new company users
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addCompanyUser(Request $request)
    {
        if ($request->isMethod('post')) {
            $user_ids = $request->get('user_ids');
            $company_id = $request->route('id');
            $company = Company::query()->find($company_id);
            $users = User::query()->whereIn('id', $user_ids)->where('type', User::LEADER)->get();
            $company->users()->attach($users);
            return BaseResponse::customResponse(
                'Creating company admin is successfully',
                [],
                true,
                201,
                201,
                'Created'
            );
        } else {
            return BaseResponse::customResponse(
                'Fail to insert company admin',
                [],
                false,
                Config('error_constant.article.insert_fail'),
                500,
                'Something went wrong'
            );
        }
    }
}
