<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 18/02/2019
 * Time: 11:11
 */

namespace App\OnePay\ATMOnline;

use App\OnePay\SaveOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DORequest
{
    /**
     * Send do request to onepay.
     * @param Request $request
     * @return string
     */
    public static function sendDORequest(Request $request)
    {
        $payment_transaction_id = 'bstar'.md5(time() . str_random(5) . str_random(5) . 'rainichi');
        $SECURE_SECRET = config('one_pay.national.secret_key');
        $vpcURL = "";
        $payment_data = $request->except('payment_type');
        $payment_data['vpc_Merchant'] = config('one_pay.national.merchant_id');
        $payment_data['vpc_AccessCode'] = config('one_pay.national.access_code');
        $payment_data['vpc_ReturnURL'] = config('one_pay.national.return_url');
        $payment_data['vpc_Command'] = "pay";
        $payment_data['vpc_Currency'] = "VND";
        $payment_data['vpc_Version'] = "2";
        $payment_data['vpc_Locale'] = "vi";
        $payment_data['vpc_TicketNo'] = $request->ip();
        $payment_data['vpc_MerchTxnRef'] = $payment_transaction_id;
        $payment_data['Title'] = "Rainichi";
        $payment_data['AgainLink'] = "http://rainichi.com";
        $payment_data['vpc_Customer_Id'] = Auth::user()->id;
        $payment_data['vpc_Customer_Email'] = Auth::user()->email;
        $payment_data['vpc_Customer_Phone'] = Auth::user()->phone;
        $payment_data['vpc_Amount'] = $request->get('vpc_Amount')*100;
        $stringHashData = "";
        $appendAmp = 0;
        ksort($payment_data);
        foreach ($payment_data as $key => $value) {
            // tạo chuỗi đầu dữ liệu những tham số có dữ liệu
            if (strlen($value) > 0) {
                // this ensures the first paramter of the URL is preceded by the '?' char
                if ($appendAmp == 0) {
                    $vpcURL .= urlencode($key) . '=' . urlencode($value);
                    $appendAmp = 1;
                } else {
                    $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
                }
                //$stringHashData .= $value; *****************************sử dụng cả tên và giá trị tham số để mã hóa*****************************
                if ((strlen($value) > 0) && ((substr($key, 0, 4) == "vpc_") || (substr($key, 0, 5) == "user_"))) {
                    $stringHashData .= $key . "=" . $value . "&";
                }
            }
        }
        //*****************************xóa ký tự & ở thừa ở cuối chuỗi dữ liệu mã hóa*****************************
        $stringHashData = rtrim($stringHashData, "&");
        // Create the secure hash and append it to the Virtual Payment Client Data if
        // the merchant secret has been provided.
        // thêm giá trị chuỗi mã hóa dữ liệu được tạo ra ở trên vào cuối url
        if (strlen($SECURE_SECRET) > 0) {
            //$vpcURL .= "&vpc_SecureHash=" . strtoupper(md5($stringHashData));
            // *****************************Thay hàm mã hóa dữ liệu*****************************
            $vpcURL .= "&vpc_SecureHash=" . strtoupper(hash_hmac('SHA256', $stringHashData, pack('H*', $SECURE_SECRET)));
        }
        SaveOrder::whenCreate($request, 'ATM_ONLINE', $payment_transaction_id);
        return config('one_pay.national.url') . '?' . $vpcURL;
    }
}
