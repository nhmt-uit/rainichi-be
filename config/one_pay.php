<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 15/02/2019
 * Time: 11:32
 */

return [
    'national' => [
        'url' => env('ONE_PAY_URL_PAYMENT_NATIONAL', 'https://mtf.onepay.vn/onecomm-pay/vpc.op'),
        'merchant_id' => env('ONE_PAY_MERCHANT_ID_NATIONAL', 'ONEPAY'),
        'access_code' => env('ONE_PAY_ACCESS_CODE_NATIONAL', 'D67342C2'),
        'secret_key' => env('ONE_PAY_HASH_CODE_NATIONAL', 'A3EFDFABA8653DF2342E8DAC29B51AF0'),
        'return_url' => env('ONE_PAY_RETURN_URL_NATIONAL', 'http://rainichi.develop/api/payment/atm/response')
    ],
    'international' => [
        'url' => env('ONE_PAY_URL_PAYMENT_INTERNATIONAL', 'https://mtf.onepay.vn/vpcpay/vpcpay.op'),
        'merchant_id' => env('ONE_PAY_MERCHANT_ID_INTERNATIONAL', 'TESTONEPAY'),
        'access_code' => env('ONE_PAY_ACCESS_CODE_INTERNATIONAL', '6BEB2546'),
        'secret_key' => env('ONE_PAY_HASH_CODE_INTERNATIONAL', '6D0870CDE5F24F34F3915FB0045120DB'),
        'return_url' => env('ONE_PAY_RETURN_URL_INTERNATIONAL', 'http://rainichi.develop/api/payment/visa/response')
    ]
];
