<?php


namespace App\VNPay\ATMOnline;

use App\VNPay\SaveOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DoRequest
{
    /**
     * This function that handle request sent to VN pay
     * @params Request $request
     * @param Request $request
     * @return string
     */

    public static function sendCreatePayment(Request $request, $paymentByCash = false)
    {
        $vnp_HashSecret = config('vn_pay.normal.secret_key'); //Secret key
        $vnp_Url = config('vn_pay.normal.url');
        $vnp_Returnurl = $paymentByCash ? config('vn_pay.normal.return_cash_url') : config('vn_pay.normal.return_url');
        $vnp_TxnRef = 'bstar' . md5(time() . str_random(5) . str_random(5) . 'rainichi'); //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = $paymentByCash ? 'OrderByCash' : 'OrderPayment';
        $vnp_OrderType = 'billpayment';
        $vnp_BankCode = '';
        $vnpSecureHash = '';

        $inputData = array(
            "vnp_Version" => "2.0.0",
            "vnp_TmnCode" => config('vn_pay.normal.merchant_id'), //Mã website tại VNPAY,
            "vnp_Amount" => $request->get('vnp_Amount') * 100,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $request->ip(),
            "vnp_Locale" => 'vn',
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . $key . "=" . $value;
            } else {
                $hashdata .= $key . "=" . $value;
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            // $vnpSecureHash = md5($vnp_HashSecret . $hashdata);
            $vnpSecureHash = hash('sha256', $vnp_HashSecret . $hashdata);
            $vnp_Url .= 'vnp_SecureHashType=SHA256&vnp_SecureHash=' . $vnpSecureHash;
        }
        if ($paymentByCash) {
            SaveOrder::whenCreateByCash($request, $vnp_BankCode, $vnp_TxnRef);
        } else {
            SaveOrder::whenCreate($request, $vnp_BankCode, $vnp_TxnRef);
        }
        return $vnp_Url;
    }
}
