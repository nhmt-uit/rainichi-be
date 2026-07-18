<?php

namespace App\Http\Controllers\API\Credit;

use App\Http\Controllers\Controller;
use App\Models\MoneyToCredit;
use App\Service\BaseResponse;
use App\Service\GoogleService;
use App\Service\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $credit_id = $request->route('id');
        $data_change = $request->except('_method');
        $credit = MoneyToCredit::find($credit_id);
        if ($credit) {

            if ($request->hasFile('image')) {
                $old_image = $credit->image;
                $image = UploadService::handleUploadFile($request->file('image'), Config('uploadpath.credit_image_folder'));
                $data_change['image'] = $image;
                if ($old_image != null) {
                    UploadService::handleRemoveFile($old_image);
                }
            }
            $credit->update($data_change);
            if ($credit->save()) {
                $googleService = new GoogleService();
                $googleService->editProduct($credit);
                return BaseResponse::customResponse(
                    'Update successfully',
                    $credit,
                    true,
                    200,
                    200,
                    'Success'
                );
            }

        } else {
            return BaseResponse::customResponse(
                'Credit not found',
                [],
                false,
                Config('error_constant.vocabulary.not_found'),
                404,
                'NotFound'
            );
        }

    }

    public function detail($id)
    {
        $credit = MoneyToCredit::query()->find($id);
        if ($credit) {
            $credit->image = media_url_web($credit->image);
            return BaseResponse::customResponse(
                'Update successfully',
                $credit,
                true,
                200,
                200,
                'Success'
            );

        } else {
            return BaseResponse::customResponse(
                'Vocabulary not found',
                [],
                false,
                Config('error_constant.credit.not_found'),
                404,
                'NotFound'
            );
        }
    }
}
