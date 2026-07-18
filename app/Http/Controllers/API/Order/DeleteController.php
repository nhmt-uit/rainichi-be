<?php

namespace App\Http\Controllers\API\Order;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Service\BaseResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeleteController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        $list_id = $request->list_id;
        if (isset($list_id)) {
            foreach ($list_id as $id) {
                $payment = OrderPayment::find($id);
                if ($payment != null) {
                    $payment->delete();
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
        }
        return BaseResponse::customResponse(
            'Fail to delete',
            [],
            false,
            Config('error_constant.vocabulary.delete_fail'),
            422,
            'Unprocessable Entity'
        );

    }

    public function deleteOrder(Request $request)
    {
        $list_id = $request->list_id;
        if (isset($list_id)) {
            foreach ($list_id as $id) {
                $payment = Order::find($id);
                if ($payment != null) {
                    $payment->delete();
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
        }
        return BaseResponse::customResponse(
            'Fail to delete',
            [],
            false,
            Config('error_constant.normal.delete_fail'),
            422,
            'Unprocessable Entity'
        );
    }
}
