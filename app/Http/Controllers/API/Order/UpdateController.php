<?php

namespace App\Http\Controllers\API\Order;

use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\User;
use App\Service\BaseResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UpdateController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveOrRejectOrder(Request $request)
    {
        $order_id = $request->route('order_id');
        $action = $request->segment(5);
        $order = Order::query()->find($order_id);
        if ($order) {
            switch ($action) {
                case 'approve':
                    $order->status = Order::DONE;
                    $order->order_msg = $request->get('order_msg');
                    break;
                case 'reject':
                    $order->status = Order::REJECT;
                    $order->order_msg = $request->get('order_msg');
                    break;
            }
            $order->save();
            return BaseResponse::customResponse(
                'Success',
                $order,
                true,
                200,
                200,
                "Success",
                []);
        } else {
            return BaseResponse::customResponse(
                'Order not found',
                [],
                true,
                404,
                404,
                "NotFound",
                []);
        }

    }
}
