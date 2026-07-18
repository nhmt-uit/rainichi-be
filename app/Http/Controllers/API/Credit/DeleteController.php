<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 03/12/2018
 * Time: 11:50
 */

namespace App\Http\Controllers\API\Credit;


use App\Http\Controllers\Controller;
use App\Models\MoneyToCredit;
use App\Models\Vocabulary;
use App\Service\BaseResponse;
use App\Service\GoogleService;
use App\Service\UploadService;
use Illuminate\Http\Request;

class DeleteController extends Controller
{
    /**
     * Delete credit by id.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $list_id = $request->list_id;
        if (isset($list_id)) {
            foreach ($list_id as $id) {
                $credit = MoneyToCredit::find($id);
                if ($credit != null) {
                    $image = $credit->image;
                    if ($credit->delete()) {
                        $googleService = new GoogleService();
                        $googleService->deleteProduct($credit);
                        if ($image != null) {
                            UploadService::handleRemoveFile($image);
                        }
                    }
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
