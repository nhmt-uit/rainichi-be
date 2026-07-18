<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/03/2019
 * Time: 17:33
 */

namespace App\OnePay;


use App\Models\OrderPayment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaveOrder
{

    /**
     * @param Request $request
     * @param $payment_type
     * @param $payment_transaction_id
     */
    public static function whenCreate(Request $request, $payment_type, $payment_transaction_id)
    {
        OrderPayment::query()->create([
            'payment_method' => $payment_type,
            'amount' => $request->get('vpc_Amount'),
            'credit' => $request->get('vpc_OrderInfo'),
            'user_id' => Auth::user()->id,
            'final_amount' => $request->get('vpc_Amount'),
            'payment_provider' => "One Pay",
            'payment_transaction_id' => $payment_transaction_id,
            'payment_status' => OrderPayment::INPROGRESS,
            'payment_msg' => "",
            'discount_code' => "",
            'discount_amount' => 0
        ]);


    }

    /**
     * @param Request $request
     */
    public static function whenSuccess(Request $request, $payment_response)
    {
        $order = OrderPayment::query()->where('payment_transaction_id', $request->query('vpc_MerchTxnRef'))->first();
        if ($order && $order->payment_status != OrderPayment::DONE) {
            $order->payment_status = OrderPayment::DONE;
            $order->payment_msg = $payment_response;
            if ($order->save()) {
                $user = User::query()->find($order->user_id);
                if ($user) {
                    $user->credits += $order->credit;
                    $user->save();
                }
            }
        }
    }
}
