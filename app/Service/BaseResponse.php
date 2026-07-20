<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 04/12/2018
 * Time: 15:21
 */

namespace App\Service;


class BaseResponse
{
    /**
     * Create base response data for client
     * @param $message
     * @param $data
     * @param $success
     * @param null $code
     * @param $http_status
     * @param $http_message
     * @param array $meta
     * @return \Illuminate\Http\JsonResponse
     */
    public static function customResponse($message, $data, $success, $code, $http_status, $http_message, $meta = [])
    {
        return response()->json([
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'meta' => $meta,
            'code' => $code,
        ])->setStatusCode($http_status, $http_message);
    }
}
