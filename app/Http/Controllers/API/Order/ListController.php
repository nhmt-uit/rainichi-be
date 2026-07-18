<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 03/12/2018
 * Time: 11:50
 */

namespace App\Http\Controllers\API\Order;


use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\RewardLog;
use App\Service\BaseResponse;
use App\Transformers\OrderPaymentEnterpriseTransformer;
use App\Transformers\OrderTransformer;
use App\Transformers\OrderPaymentTransformer;
use App\Transformers\RewardTransformer;
use App\Transformers\TransactionTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

class ListController extends Controller
{
    const VOCABULARY = 1;
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var OrderTransformer
     */
    private $orderTransformer;
    /**
     * @var OrderPaymentTransformer
     */
    private $orderPaymentTransformer;

    /**
     * @var OrderPaymentEnterpriseTransformer
     */
    private $orderPaymentEnterpriseTransformer;

    /**
     * @var TransactionTransformer
     */
    private $transactionTransformer;

    /**
     * @var RewardTransformer
     */
    private $rewardTransformer;


    function __construct(Manager $fractal, OrderTransformer $orderTransformer,
                         OrderPaymentTransformer $orderPaymentTransformer,
                         TransactionTransformer $transactionTransformer,
                         RewardTransformer $rewardTransformer,
                         OrderPaymentEnterpriseTransformer $orderPaymentEnterpriseTransformer)
    {
        $this->fractal = $fractal;
        $this->orderTransformer = $orderTransformer;
        $this->orderPaymentTransformer = $orderPaymentTransformer;
        $this->transactionTransformer = $transactionTransformer;
        $this->orderPaymentEnterpriseTransformer = $orderPaymentEnterpriseTransformer;
        $this->rewardTransformer = $rewardTransformer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $search_string = $request->query('search_string');
        $from_date = $request->query('from_date');
        $to_date = $request->query('to_date');
        $customer_type_id = $request->query('customer_type_id') ?? Order::CUSTOMER_PERSONAL;
        $order_list = Order::query()
            ->with(['user', 'course', 'exam'])
            ->dateRange($from_date,$to_date)
            ->customerType($customer_type_id)
            ->searchString($search_string)
            ->orderByDesc('id')
            ->paginate($paging);

        $orders = new Collection($order_list->items(), $this->orderTransformer);
        $orders->setPaginator(new IlluminatePaginatorAdapter($order_list));
        $orders = $this->fractal->createData($orders);
        return BaseResponse::customResponse(
            'Get list successfully',
            $orders->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $orders->toArray()['meta']
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function orderPayment(Request $request)
    {
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $search_string = $request->query('search_string');
        $from_date = $request->query('from_date');
        $to_date = $request->query('to_date');
        $is_enterprise = $request->query('is_enterprise') ?? 0;
        $payment_status = $request->query('payment_status');
        $payment_method = $request->query('payment_method');
        $lang = $request->query('lang') ?? 'vn';
        $order_list = OrderPayment::query()
            ->with(['user', 'paymentCourse'])
            ->dateRange($from_date,$to_date)
            ->isEnterprise($is_enterprise)
            ->paymentStatus($payment_status)
            ->paymentMethod($payment_method)
            ->searchBy($search_string, $lang, $is_enterprise)
            ->orderByDesc('id')
            ->paginate($paging);

        $orders = $is_enterprise ? new Collection($order_list->items(), $this->orderPaymentEnterpriseTransformer)
            :  new Collection($order_list->items(), $this->orderPaymentTransformer);
        $orders->setPaginator(new IlluminatePaginatorAdapter($order_list));
        $orders = $this->fractal->createData($orders);
        return BaseResponse::customResponse(
            'Get list successfully',
            $orders->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $orders->toArray()['meta']
        );
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOrderListByUser(Request $request)
    {
        if ($request->query('user_id')) {
            $user_id = $request->query('user_id');
        } else {
            $user_id = Auth::user()->id;
        }
        $orders = Order::query()->where('user_id', $user_id)
            ->whereNull('classroom_id')
            ->orderByDesc('id')->get();
        $orders = new Collection($orders, $this->transactionTransformer);
        $orders = $this->fractal->createData($orders);
        $payments = OrderPayment::query()->where('user_id', $user_id)
                                         ->where('payment_status', OrderPayment::DONE)
                                         ->where('is_enterprise', false)
                                         ->orderByDesc('id')->get();
        $payments = new Collection($payments, $this->orderPaymentTransformer);
        $payments = $this->fractal->createData($payments);
        $rewards = RewardLog::query()->where('user_id', $user_id)->get();
        $rewards = new Collection($rewards, $this->rewardTransformer);
        $rewards = $this->fractal->createData($rewards);
        $transaction = [
            'orders' => $orders->toArray()['data'],
            'payments' => $payments->toArray()['data'],
            'rewards' => $rewards->toArray()['data']
        ];
        return BaseResponse::customResponse(
            'Get list successfully',
            $transaction,
            true,
            200,
            200,
            'Success',
            []
        );
    }

}
