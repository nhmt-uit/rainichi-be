<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 15/02/2019
 * Time: 11:29
 */

namespace App\Http\Controllers\API\Payment;

use App\Http\Controllers\Controller;
use App\Models\OrderPayment;
use App\Models\OrderPaymentCourse;
use App\Models\User;
use App\OnePay\ATMOnline\DORequest;
use App\VNPay\ATMOnline\DoRequest as VnPayRequest;
use App\OnePay\ATMOnline\DRResponse;
use App\VNPay\ATMOnline\DoResponse as VnPayResponse;
use App\Service\BaseResponse;
use App\Service\ClassroomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitPayment(Request $request)
    {
        if ($request->payment_type == "ATM") {

            $returnUrl = DORequest::sendDORequest($request);

            return BaseResponse::customResponse(
                'Success to connect',
                [
                    'redirect_url' => $returnUrl
                ],
                true,
                200,
                200,
                "Success",
                []);
        } else {
            $returnUrl = \App\OnePay\Visa\DORequest::sendDORequest($request);

            return BaseResponse::customResponse(
                'Success to connect',
                [
                    'redirect_url' => $returnUrl
                ],
                true,
                200,
                200,
                "Success",
                []);
        }
    }

    public function submitVnPayment(Request $request)
    {
        $returnUrl = VnPayRequest::sendCreatePayment($request);
        return BaseResponse::customResponse(
            'Success to connect',
            [
                'redirect_url' => $returnUrl
            ],
            true,
            200,
            200,
            "Success",
            []);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getResponseVnPay(Request $request)
    {
        $response = VnPayResponse::getResponsePayment($request);
        return view('payment.payment-result', [
            'response' => $response
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getResponseATM(Request $request)
    {
        $response = DRResponse::getDRResponse($request);
        return view('payment.payment-result', [
            'response' => $response
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getResponseVisa(Request $request)
    {
        $response = \App\OnePay\Visa\DRResponse::getDRResponse($request);
        return view('payment.payment-result', [
            'response' => $response
        ]);
    }


    /**
     * @param Request $request
     */
    public function ipnATM(Request $request)
    {
        $response = DRResponse::getDRResponse($request);
        if ($response['code'] == '0') {
            echo "responsecode=1&desc=confirm-success";
        } else {
            echo "responsecode=0&desc=confirm-fail";
        }
    }

    /**
     * @param Request $request
     */
    public function ipnVisa(Request $request)
    {
        $response = \App\OnePay\Visa\DRResponse::getDRResponse($request);
        if ($response['code'] == '0') {
            echo "responsecode=1&desc=confirm-success";
        } else {
            echo "responsecode=0&desc=confirm-fail";
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function offlinePayment(Request $request)
    {
        $is_enterprise = $request->get('is_enterprise');
        $data = $request->all();
        $order_payment = OrderPayment::query()->create([
            'payment_method' => $request->get('payment_type'),
            'amount' => $request->get('vpc_Amount'),
            'credit' => $request->get('vpc_OrderInfo') ?? 0,
            'user_id' => Auth::user()->id,
            'final_amount' => $request->get('vpc_Amount'),
            'payment_provider' => OrderPayment::DEFAULT_PROVIDER,
            'payment_transaction_id' => null,
            'payment_status' => OrderPayment::INPROGRESS,
            'payment_msg' => OrderPayment::TRANSFER ? "Payment by bank transfer" : "Payment offline in office",
            'discount_code' => "",
            'discount_amount' => 0,
            'created_at' => Auth::user()->id,
            'is_enterprise' => $is_enterprise ?? 0
        ]);
        if (key_exists('is_enterprise', $request->all()) && $request->get('is_enterprise')) {
            $oderData = [
                'test_id' => $data['test_id'] ?? null,
                'course_id' => $data['course_id'] ?? null,
                'classroom_id' => $data['classroom_id'] ?? null,
                'order_payment_id' => $order_payment->id,
                'num_of_employee' => $data['num_of_employee'],
                'duration' => isset($data['duration']) ? $data["duration"] : 0,
                'course_price_currency_id' => OrderPaymentCourse::CURRENCY_VND
            ];
            OrderPaymentCourse::firstOrCreate($oderData);
            ClassroomService::createDefaultClassroom($order_payment, $data);
        }
        if ($order_payment) {
            return BaseResponse::customResponse(
                'Waiting for confirm',
                $order_payment,
                true,
                200,
                200,
                "Success",
                []);
        } else {
            return BaseResponse::customResponse(
                'Fail to create payment',
                [],
                true,
                422,
                422,
                "Success",
                []);
        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function approveOrRejectOrder(Request $request)
    {
        $order_id = $request->route('order_id');
        $action = $request->segment(4);
        $order = OrderPayment::query()->find($order_id);
        if ($order) {
            $user = User::query()->find($order->user_id);
            $order->updated_by = Auth::user()->id;
            switch ($action) {
                case 'approve':
                    $order->payment_status = OrderPayment::DONE;
                    $order->payment_msg = $request->get('payment_msg');
                    if ($order->is_enterprise) {
                        ClassroomService::rejectOrApprovedCourseClass($order, true);
                    } else {
                        $user->credits += $order->credit;
                    }
                    break;
                case 'reject':
                    $order->payment_status = OrderPayment::INPROGRESS;
                    $order->payment_msg = $request->get('payment_msg');
                    if ($order->is_enterprise) {
                        ClassroomService::rejectOrApprovedCourseClass($order, false);
                    } else {
                        $user->credits > $order->credit ? $user->credits -= $order->credit : $user->credits = 0;
                    }

                    break;
            }
            if ($order->save()) {
                $user->save();
            }
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
