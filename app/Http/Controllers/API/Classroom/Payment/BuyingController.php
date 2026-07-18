<?php


namespace App\Http\Controllers\API\Classroom\Payment;


use App\Http\Controllers\Controller;
use App\Service\BaseResponse;
use App\VNPay\ATMOnline\DoRequest as VnPayRequest;
use App\VNPay\ATMOnline\DoResponse as VnPayResponse;
use Illuminate\Http\Request;

class BuyingController extends Controller
{
    public function createPaymentRequest(Request $request)
    {
        $paymentByCash = true;
        $returnUrl = VnPayRequest::sendCreatePayment($request, $paymentByCash);
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

    public function getPaymentResponse(Request $request)
    {
        $response = VnPayResponse::getResponsePayment($request);
        return view('payment.payment-result', [
            'response' => $response
        ]);
    }
}
