<?php

namespace App\Http\Controllers\API\Country;

use App\Models\Country;
use App\Service\BaseResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ListController extends Controller
{

    /**
     * Show all list country 
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public  function  getList(Request $request)
    {
        $countries = Country::query()->isActive(true)->get();
        return BaseResponse::customResponse(
            __('Get articles successfully'),
            $countries->toArray(),
            true,
            200,
            200,
            'Success'
        );
    }
}
