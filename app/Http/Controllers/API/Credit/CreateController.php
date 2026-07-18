<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 03/12/2018
 * Time: 10:28
 */

namespace App\Http\Controllers\API\Credit;


use App\Http\Controllers\Controller;
use App\Models\MoneyToCredit;
use App\Service\BaseResponse;
use App\Service\GoogleService;
use App\Service\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{

    /**
     * Add new credit.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $credit_data = $request->all();
        //check image
        if ($request->hasFile('image')) {

            $image = UploadService::handleUploadFile($request->file('image'), Config('uploadpath.credit_image_folder'));
            $credit_data['image'] = $image;
        } else {
            $credit_data['image'] = null;
        }
        $credit_data['created_by'] = Auth::user()->id;
        $credit = MoneyToCredit::create($credit_data);

        $googleService = new GoogleService();
        $res = $googleService->insertProduct($credit);

        $credit->package_id = $res->getSku();


        if ($credit->save()) {
            return BaseResponse::customResponse(
                'Create successfully',
                $credit,
                true,
                200,
                201,
                'Created'
            );
        } else {
            return BaseResponse::customResponse(
                'Fail to insert',
                [],
                false,
                Config('error_constant.vocabulary.insert_fail'),
                422,
                'Unprocessable Entity'
            );
        }

    }
}
