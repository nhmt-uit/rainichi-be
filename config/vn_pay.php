<?php
/**
 * Created by Thach Nguyen.
 */

return [
    'normal' => [
        'url' => env('VN_PAY_URL_PAYMENT', 'http://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
        'merchant_id' => env('VN_PAY_TMN_CODE', 'LABOVN01'),
        'secret_key' => env('VN_PAY_HASH_CODE', 'ZBHKDBLMLXOLGMJHONEATQOAHLNFCWOX'),
        'return_url' => env('VN_PAY_RETURN_URL', 'http://rainichi.develop/api/payment/atm/response'),
        'return_cash_url' => env('VN_PAY_CASH_RETURN_URL', 'http://rainichi.com'),
        'fe_return_url' => env('VN_PAY_FE_RETURN_URL', 'http://rainichi.develop/api/payment/atm/response')
    ],
];
