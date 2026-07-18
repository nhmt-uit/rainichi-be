<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\Order;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class OrderTransformer extends TransformerAbstract
{

    public function transform(Order $order)
    {
        return [
            'id' => $order->id,
            'user' => $order->user,
            'course' => $order->course_id ? $order->course : ($order->test_id ? $order->exam : null),
            'buying_credits' => $order->buying_credits,
            'reward_credits' => $order->reward_credits,
            'discount_credits' => $order->discount_credits,
            'customer_type_id' => $order->customer_type_id,
            'course_price_currency_id' => $order->course_price_currency_id,
            'payment_method' => $order->payment_method,
            'status' => $order->status,
            'order_msg' => $order->order_msg,
            'classroom_id' => $order->classroom_id,
            'classroom' => $order->classroom ?? null,
            'start_date' => $order->start_date ? Carbon::parse($order->start_date)->format('d-m-Y h:m') : null,
            'end_date' => $order->end_date ? Carbon::parse($order->end_date)->format('d-m-Y h:m') : null,
            'created_at' => Carbon::parse($order->created_at)->format('d-m-Y'),
            'updated_at' => Carbon::parse($order->updated_at)->format('d-m-Y'),
        ];
    }
}
