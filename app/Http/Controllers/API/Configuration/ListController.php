<?php

namespace App\Http\Controllers\API\Configuration;

use App\Models\Configuration;
use App\Service\BaseResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Transformers\ConfigurationTransformer;
use App\Transformers\ConfigurationAdminTransformer;

class ListController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $is_admin = $request->segment(3) == 'admin' ? true : false;
        $configsData = null;
        $config = Configuration::query()->first();
        if ($config) {
            $configsData = $is_admin ? (new ConfigurationAdminTransformer)->transform($config)
                : (new ConfigurationTransformer)->transform($config);
        }
        return BaseResponse::customResponse(
            'Get data successfully',
            $configsData,
            true,
            200,
            200,
            'Success'
        );
    }
}
