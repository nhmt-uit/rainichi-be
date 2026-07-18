<?php


namespace App\VNPay\ATMOnline;

use App\Models\OrderByCash;
use App\Models\OrderPayment;
use App\VNPay\SaveOrder;
use Illuminate\Http\Request;

class IPN
{
    /**
     * IPN request provide for vnpay to confirm order
     * @param Request $request
     * @return false|string
     */
    public static function index(Request $request)
    {
        $vnp_HashSecret = config('vn_pay.normal.secret_key'); //Secret key
        $inputData = array();
        $returnData = array();
        $data = $request->all();
        foreach ($data as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        $vnp_SecureHash = $inputData['vnp_SecureHash'];
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
        $vnpOrderId = $inputData['vnp_TxnRef']; //Order id
        $orderInfo = $inputData['vnp_OrderInfo'];
        $secureHash = hash('sha256', $vnp_HashSecret . $hashData);

        try {
            if ($secureHash == $vnp_SecureHash) {
                // Get order by transaction id
                if ($orderInfo === 'OrderPayment') {
                    $order = OrderPayment::query()->where('payment_transaction_id', $vnpOrderId)->first();
                } else {
                    $order = OrderByCash::query()->where('payment_transaction_id', $vnpOrderId)->first();
                }
                if ($order != NULL) {
                    if (floatval($inputData['vnp_Amount']) === floatval($order->amount * 100)) {
                        if ($order->payment_status != OrderPayment::FAILED && $order->payment_status == OrderPayment::INPROGRESS) {
                            if ($inputData['vnp_ResponseCode'] == '00') {
                                $orderInfo === 'OrderByCash' ? SaveOrder::whenCashSuccess($request, 'Thành công')
                                    :SaveOrder::whenSuccess($request, 'Thành công.');
                            }

                            $returnData['RspCode'] = '00';
                            $returnData['Message'] = 'Confirm Success';
                        } else {
                            $returnData['RspCode'] = '02';
                            $returnData['Message'] = 'Order already confirmed';
                        }


                    } else {
                        $returnData['RspCode'] = '04';
                        $returnData['Message'] = 'Invalid amount';
                    }
                } else {
                    $returnData['RspCode'] = '01';
                    $returnData['Message'] = 'Order not found';
                }
            } else {
                $returnData['RspCode'] = '97';
                $returnData['Message'] = 'Chu ky khong hop le';
            }
        } catch (\Exception $e) {
            $returnData['RspCode'] = '99';
            $returnData['Message'] = 'Unknow error';
        }
        return json_encode($returnData);

    }
}
