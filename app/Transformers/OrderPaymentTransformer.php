<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\CourseClass;
use App\Models\OrderPayment;
use Carbon\Carbon;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class OrderPaymentTransformer extends TransformerAbstract
{
    public function transform(OrderPayment $order)
    {
        return [
            'id' => $order->id,
            'payment_method' => $order->payment_method,
            'amount' => $order->final_amount,
            'credit' => $order->credit,
            'user' => $order->user,
            'payment_provider' => $order->payment_provider,
            'payment_transaction_id' => $order->payment_transaction_id,
            'payment_status' => $order->payment_status,
            'payment_msg' => $order->payment_msg,
            'discount_amount' => $order->discount_amount,
            'discount_code' => $order->discount_code,
            'is_enterprise' => $order->is_enterprise,
            'created_at' => convertAsiaDate($order->created_at),
        ];
    }
}
