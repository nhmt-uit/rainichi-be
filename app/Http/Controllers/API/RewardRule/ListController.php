<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 03/12/2018
 * Time: 11:50
 */

namespace App\Http\Controllers\API\RewardRule;


use App\Http\Controllers\Controller;
use App\Models\MoneyToCredit;
use App\Models\RewardRule;
use App\Service\BaseResponse;
use App\Transformers\CreditTransformer;
use App\Transformers\RewardRuleTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

class ListController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var RewardRuleTransformer
     */
    private $rewardRuleTransformer;

    function __construct(Manager $fractal, RewardRuleTransformer $rewardRuleTransformer)
    {
        $this->fractal = $fractal;
        $this->rewardRuleTransformer = $rewardRuleTransformer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $column = $request->query('column');
        $is_active = $request->get('is_active');
        $order_by_type = $request->query('order_by_type');
        $search_string = $request->query('search_string');
        $credit_list = RewardRule::query()
            ->searchBy($search_string)
            ->orderByCustom($column, $order_by_type)
            ->vocabularyActive($is_active)->get();
        $credit_list = new Collection($credit_list, $this->rewardRuleTransformer);
        $credit_list = $this->fractal->createData($credit_list);
        return BaseResponse::customResponse(
            'Get list successfully',
            $credit_list->toArray()['data'],
            true,
            200,
            200,
            'Success',
            []
        );
    }


}
