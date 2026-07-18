<?php
/**
 * Created by PhpStorm.
 * User: ThachNguyen
 * Date: 18/06/2019
 */

namespace App\VNPay;

use App\Models\OrderByCash;
use App\Models\OrderPayment;
use App\Models\OrderPaymentCourse;
use App\Models\User;
use App\Service\ClassroomService;
use Illuminate\Config\Repository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaveOrder
{

    /**
     * @param Request $request
     * @param $payment_type
     * @param $payment_transaction_id
     * @return Builder|Model
     */

    public static function whenCreate(Request $request, $payment_type, $payment_transaction_id)
    {
        $order = OrderPayment::query()->create([
            'payment_method' => $payment_type,
            'amount' => $request->get('vnp_Amount'),
            'credit' => $request->get('vnp_OrderInfo'),
            'user_id' => Auth::user()->id,
            'final_amount' => $request->get('vnp_Amount'),
            'payment_provider' => "Vn Pay",
            'payment_transaction_id' => $payment_transaction_id,
            'payment_status' => OrderPayment::INPROGRESS,
            'payment_msg' => "",
            'discount_code' => "",
            'discount_amount' => 0,
            'is_enterprise' => $request->get('is_enterprise') ?? 0,
            'return_url' => $request->get('return_url') ?? null,
        ]);
        if (key_exists('is_enterprise', $request->all())) {
            $oderData = [
                'test_id' => $request->get('test_id') ?? null,
                'course_id' => $request->get('course_id') ?? null,
                'classroom_id' => $request->get('classroom_id') ?? null,
                'order_payment_id' => $order->id,
                'num_of_employee' => $request->get('num_of_employee'),
                'duration' => $request->get('duration') ?? 0,
                'course_price_currency_id' => OrderPaymentCourse::CURRENCY_VND
            ];
            OrderPaymentCourse::firstOrCreate($oderData);
        }
        return $order;
    }

    /**
     * @param Request $request
     * @param $payment_type
     * @param $payment_transaction_id
     * @return Builder|Model
     */
    public static function whenCreateByCash(Request $request, $payment_type, $payment_transaction_id)
    {
        $discount_amount = $request->get('discount_amount') ?? 0;
        $order = OrderByCash::query()->create([
            'user_id' => Auth::user()->id,
            'course_id' => $request->get('course_id'),
            'test_id' => $request->get('test_id'),
            'payment_provider' => "Vn Pay",
            'payment_method' => $payment_type,
            'payment_transaction_id' => $payment_transaction_id,
            'payment_status' => OrderByCash::PENDING,
            'payment_msg' => "",
            'amount' => $request->get('vnp_Amount'),
            'discount_code' => $request->get('discount_code'),
            'discount_amount' => $discount_amount,
            'final_amount' => $request->get('vnp_Amount') - $discount_amount
        ]);
        return $order;
    }

    /**
     * @param Request $request
     * @param $payment_response
     * @return Repository|mixed
     */
    public static function whenSuccess($request, $payment_response)
    {
        $vnpTxnRef = $request->query('vnp_TxnRef');
        $paymentMethod = self::parsePaymentMethod($request->query('vnp_CardType'));
        $bankCode = $request->query('vnp_BankCode');
        $order = OrderPayment::query()->where('payment_transaction_id', $vnpTxnRef)->first();
        if ($order && $order->payment_status != OrderPayment::DONE) {
            $order->payment_status = OrderPayment::DONE;
            $order->payment_method = $paymentMethod;
            $order->payment_msg = $payment_response . ' tại Ngân hàng ' . $bankCode;
            if ($order->save()) {
                $user = User::query()->find($order->user_id);
                if ($order->is_enterprise) {
                    ClassroomService::createDefaultClassroom($order);
                    ClassroomService::rejectOrApprovedCourseClass($order, true);
                } else {
                    $user->credits += $order->credit;
                    $user->save();
                }
            }
        }
    }

    /**
     * @param $request
     * @param $payment_response
     */
    public static function whenCashSuccess($request, $payment_response)
    {
        $vnpTxnRef = $request->query('vnp_TxnRef');
        $paymentMethod = self::parsePaymentMethod($request->query('vnp_CardType'));
        $bankCode = $request->query('vnp_BankCode');
        $order = OrderByCash::query()->where('payment_transaction_id', $vnpTxnRef)->first();
        if ($order && $order->payment_status != OrderByCash::DONE) {
            $order->payment_status = OrderByCash::DONE;
            $order->payment_method = $paymentMethod;
            $order->payment_msg = $payment_response . ' tại Ngân hàng ' . $bankCode;
            $order->save();
        }
    }

    /**
     * @param $cart_type
     * @return string
     */
    public static function parsePaymentMethod($cart_type)
    {
        return $cart_type == 'ATM' ? 'ATM_ONLINE' : $cart_type;
    }
}
