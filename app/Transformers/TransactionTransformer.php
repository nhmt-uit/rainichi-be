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

class TransactionTransformer extends TransformerAbstract
{

    public function transform(Order $order)
    {
        return [
            'course' => self::getName($order),
            'buying_credits' => $order->buying_credits,
            'start_date' => convertAsiaDate($order->created_at),
        ];
    }

    static function getName($order)
    {
        if ($order->course_id) {
            return ['translations' => $order->course->getTranslationsArray()];
        } elseif ($order->test_id) {
            return ['translations' => $order->exam->getTranslationsArray()];
        } else {
            return null;
        }
    }
}
