<?php

namespace App\VNPay\ATMOnline;

use App\Models\OrderPayment;
use App\VNPay\SaveOrder;
use Illuminate\Http\Request;

class DoResponse
{
    /**
     * This function that handle receive data from VNP
     * @param Request $request
     * @return array
     */
    public static function getResponsePayment(Request $request, $paymentByCash = false)
    {

        $responeCode = $request->query('vnp_ResponseCode');
        $vnp_HashSecret = config('vn_pay.normal.secret_key'); //Secret key
        $vnpTxnRef = $request->query('vnp_TxnRef');
        $vnp_SecureHash = $request->query('vnp_SecureHash');
        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }
        unset($inputData['vnp_SecureHashType']);
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . $key . "=" . $value;
            } else {
                $hashData = $hashData . $key . "=" . $value;
                $i = 1;
            }
        }

        //$secureHash = md5($vnp_HashSecret . $hashData);
        $secureHash = hash('sha256', $vnp_HashSecret . $hashData);
        if ($secureHash == $vnp_SecureHash) {


            $order = OrderPayment::query()->where('payment_transaction_id', $vnpTxnRef)->first();
            if ($order) {
                if (floatval($request->get('vnp_Amount')) === floatval($order->amount * 100)) {

                    if ($responeCode == '00' && $order->payment_status !== OrderPayment::DONE) {
                        $paymentResponse = [
                            'message' => self::getResponseText($responeCode),
                            'code' => $responeCode,
                            'return_link' => $order->return_url ?? config('vn_pay.normal.fe_return_url')
                        ];
                        return $paymentResponse;
                    } else {
                        return [
                            'message' => 'Giao dịch đã được xác nhận - Không thể cập nhật',
                            'code' => '01',
                            'return_link' => $order->return_url ?? config('vn_pay.normal.fe_return_url')
                        ];
                    }

                } else {
                    return [
                        'message' => 'Số tiền không hợp lệ - Giao dịch thất bại',
                        'code' => '01',
                        'return_link' => $order->return_url ?? config('vn_pay.normal.fe_return_url')
                    ];
                }

            } else {
                return [
                    'message' => 'Mã hóa đơn không tồn tại- Giao dịch thất bại',
                    'code' => '01',
                    'return_link' => $order->return_url ?? config('vn_pay.normal.fe_return_url')
                ];
            }
        } else {
            return [
                'message' => 'Chữ ký không hợp lệ - Giao dịch thất bại.',
                'code' => '01',
                'return_link' => $order->return_url ?? config('vn_pay.normal.fe_return_url')
            ];
        }

    }

    /**
     * Show detail response from server
     * @param $code
     * @return string
     */
    static function getResponseText($code)
    {
        switch ($code) {
            case "00" :
                $result = "Giao dịch thành công - Approved";
                break;
            case "01" :
                $result = "Giao dịch đã tồn tại";
                break;
            case "02" :
                $result = "Merchant không hợp lệ (kiểm tra lại vnp_TmnCode)";
                break;
            case "03" :
                $result = 'Dữ liệu gửi sang không đúng định dạng';
                break;
            case "04" :
                $result = 'Khởi tạo GD không thành công do Website đang bị tạm khóa';
                break;
            case "05" :
                $result = 'Giao dịch không thành công do: Quý khách nhập sai mật khẩu quá số lần quy định. Xin quý khách vui lòng thực hiện lại giao dịch';
                break;
            case "13" :
                $result = 'Giao dịch không thành công do Quý khách nhập sai mật khẩu xác thực giao dịch (OTP). Xin quý khách vui lòng thực hiện lại giao dịch.';
                break;
            case "07" :
                $result = 'Giao dịch bị nghi ngờ là giao dịch gian lận';
                break;
            case "09" :
                $result = 'Giao dịch không thành công do: Thẻ/Tài khoản của khách hàng chưa đăng ký dịch vụ InternetBanking tại ngân hàng.';
                break;
            case "10" :
                $result = 'Giao dịch không thành công do: Khách hàng xác thực thông tin thẻ/tài khoản không đúng quá 3 lần';
                break;
            case "11" :
                $result = "Giao dịch không thành công do: Đã hết hạn chờ thanh toán. Xin quý khách vui lòng thực hiện lại giao dịch.";
                break;
            case "99" :
                $result = "Lỗi không xác định";
                break;
            default :
                $result = "Giao dịch thất bại - Failured";
        }
        return $result;
    }

}
