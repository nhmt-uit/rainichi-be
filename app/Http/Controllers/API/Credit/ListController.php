<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 03/12/2018
 * Time: 11:50
 */

namespace App\Http\Controllers\API\Credit;


use App\Http\Controllers\Controller;
use App\Models\MoneyToCredit;
use App\Service\BaseResponse;
use App\Transformers\CreditTransformer;
use Illuminate\Http\Request;
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
     * @var CreditTransformer
     */
    private $creditTransformer;

    function __construct(Manager $fractal, CreditTransformer $creditTransformer)
    {
        $this->fractal = $fractal;
        $this->creditTransformer = $creditTransformer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $created_by = $request->query('created_by');
        $column = $request->query('column');
        $is_active = $request->get('is_active');
        $order_by_type = $request->query('order_by_type');
        $search_string = $request->query('search_string');
        $credit_list = MoneyToCredit::query()->with('user')
            ->searchBy($search_string)
            ->searchByCreatedBy($created_by)
            ->orderByCustom($column, $order_by_type)
            ->vocabularyActive($is_active)
            ->paginate($paging);

        $credit = new Collection($credit_list->items(), $this->creditTransformer);
        $credit->setPaginator(new IlluminatePaginatorAdapter($credit_list));
        $credit = $this->fractal->createData($credit);
        return BaseResponse::customResponse(
            'Get list successfully',
            $credit->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $credit->toArray()['meta']
        );
    }


}
