<?php
/**
 * Created by PhpStorm.
 * User: phong.do
 * Date: 2019-11-20
 * Time: 09:46
 */

namespace App\Http\Controllers\API\Payment;


use App\Http\Controllers\Controller;
use App\Models\MoneyToCredit;
use App\Models\OrderPayment;
use App\Service\BaseResponse;
use App\Service\GoogleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use phpseclib\Crypt\RSA;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class IAPController extends Controller
{


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateIAPReceipt(Request $request)
    {


        //Verify orderId

        $signature = $request->signature;


        if ($signature == null) {
            return $this->validateIOSReceipt($request);
        } else {
            $receipt = $request->get('receipt');
            $orderCount = OrderPayment::query()->where('payment_provider', OrderPayment::GOOGLE_PLAY_PROVIDER)
                ->where("payment_transaction_id", $receipt["orderId"])->count();
            if ($orderCount > 0) {
                return BaseResponse::customResponse(
                    'This is old order',
                    [],
                    false,
                    Config('error_constant.iap.order_duplicate'),
                    422,
                    'Unprocessable Entity'
                );
            }
            $googleService = new GoogleService;
            $isValidReceipt = $googleService->verifySignature($receipt, $signature);

            if (!$isValidReceipt) {
                return BaseResponse::customResponse(
                    'This receipt is invalid',
                    [],
                    false,
                    Config('error_constant.iap.invalid_receipt'),
                    422,
                    "Failed");
            }

            $response = $googleService->validateReceiptAndroid($receipt['packageName'], $receipt['productId'], $receipt['purchaseToken']);
            Log::debug("response" . json_encode($response));

            if ($response->getPurchaseState() == 0 && $response->getConsumptionState() == 0) {
                //
                $package = MoneyToCredit::query()->where("package_id", $receipt["productId"])->first();
                if ($package) {
                    $user = $request->user();
                    $user->credits += $package->credit;
                    $user->save();
                    $this->saveOrder($package, OrderPayment::GOOGLE_PLAY_PROVIDER, $receipt["orderId"]);

                    // OK
                    return BaseResponse::customResponse(
                        'Success to connect',
                        $response,
                        true,
                        200,
                        200,
                        "Success"
                    );
                } else {
                    return BaseResponse::customResponse(
                        'Wrong package',
                        [],
                        false,
                        Config('error_constant.iap.invalid_receipt'),
                        422,
                        "Failed");
                }

            } else {
                return BaseResponse::customResponse(
                    'This purchase is already consumed',
                    [],
                    false,
                    Config('error_constant.iap.invalid_receipt'),
                    422,
                    "Failed");
            }
        }


    }

    public function test(Request $request)
    {
        $googleService = new GoogleService;
        return BaseResponse::customResponse(
            'Success to connect',
            $googleService->getAllProduct(),
            true,
            200,
            200,
            "Success"
        );

    }

    private function validateIOSReceipt($request)
    {
        $receipt = $request->get('receipt');
        $orderCount = OrderPayment::query()->where('payment_provider', OrderPayment::APPLE_PLAY_PROVIDER)
            ->where("payment_transaction_id", $receipt["transactionId"])->count();
        if ($orderCount > 0) {
            return BaseResponse::customResponse(
                'This is old order',
                [],
                false,
                Config('error_constant.iap.order_duplicate'),
                422,
                'Unprocessable Entity'
            );
        }

        $http = new \GuzzleHttp\Client;
        //PRD
//        $url = 'https://buy.itunes.apple.com/verifyReceipt';
        // Sandbox
        $url = 'https://sandbox.itunes.apple.com/verifyReceipt';

//        $request_param = [
//            'receipt-data' => $receipt["transactionReceipt"],
//        ];
//
//        $response = $http->post($url, [
//            'headers' => ['Content-Type' => 'application/json'],
//            'json' => $request_param,
//        ]);


        $res = $this->sendValidateRequest('https://buy.itunes.apple.com/verifyReceipt', $receipt);

        $status =  $res['status'];
        if ($status == 21007){
            $res = $this->sendValidateRequest('https://sandbox.itunes.apple.com/verifyReceipt', $receipt);

//            $url = 'https://sandbox.itunes.apple.com/verifyReceipt';
//            $response = $http->post($url, [
//                'headers' => ['Content-Type' => 'application/json'],
//                'json' => $request_param,
//            ]);
//            $res = json_decode((string)$response->getBody(), true);

        }
        $res = $res['receipt'];

        $inAppProduct = array_values($res['in_app'])[0];

        $transactionId = $inAppProduct["transaction_id"];
        if ($transactionId != $receipt["transactionId"]) {
            return BaseResponse::customResponse(
                'Wrong order',
                [],
                false,
                Config('error_constant.iap.order_duplicate'),
                422,
                'Unprocessable Entity'
            );
        }

        $package = MoneyToCredit::query()->where("package_id", $inAppProduct["product_id"])->first();
        if ($package) {
            $user = $request->user();
            $user->credits += $package->credit;
            $user->save();

            $this->saveOrder($package, OrderPayment::APPLE_PLAY_PROVIDER, $transactionId);
            // OK
            return BaseResponse::customResponse(
                'Success to connect',
                $inAppProduct,
                true,
                200,
                200,
                "Success"
            );
        } else {
            return BaseResponse::customResponse(
                'Wrong package',
                [],
                false,
                Config('error_constant.iap.invalid_receipt'),
                422,
                "Failed");
        }
    }

    private function sendValidateRequest($url, $receipt){
        $request_param = [
            'receipt-data' => $receipt["transactionReceipt"],
        ];
        $http = new \GuzzleHttp\Client;

        $response = $http->post($url, [
            'headers' => ['Content-Type' => 'application/json'],
            'json' => $request_param,
        ]);
        $res = json_decode((string)$response->getBody(), true);
        return $res;
    }


    private function saveOrder($package, $provider, $transactionId)
    {
        OrderPayment::query()->create([
            'payment_method' => OrderPayment::IN_APP,
            'amount' => $package->discount,
            'credit' => $package->credit,
            'user_id' => Auth::user()->id,
            'final_amount' => $package->discount,
            'payment_provider' => $provider,
            'payment_transaction_id' => $transactionId,
            'payment_status' => OrderPayment::DONE,
            'payment_msg' => "Done",
            'discount_code' => "",
            'discount_amount' => 0,
            'is_enterprise' => 0
        ]);
    }
}
