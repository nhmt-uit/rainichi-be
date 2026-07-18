<?php


namespace App\Http\Controllers\API\Payment;


use App\Http\Controllers\Controller;
use App\VNPay\ATMOnline\IPN;
use Illuminate\Http\Request;

class IPNController extends Controller
{
    /**
     * @param Request $request
     * @return false|string
     */
    public function index(Request $request)
    {
        return IPN::index($request);
    }
}
