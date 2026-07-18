<?php

namespace App\Http\Controllers\API\Promotion;

use App\Models\Promotion;
use App\Service\BaseResponse;
use App\Transformers\PromotionTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
     * @var PromotionTransformer
     */
    private $promotionTransformer;

    function __construct(Manager $fractal, PromotionTransformer $promotionTransformer) {
        $this->fractal = $fractal;
        $this->promotionTransformer = $promotionTransformer;
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function getListAdmin(Request $request)
    {
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $created_by = $request->query('created_by');
        $column = $request->query('column');
        $order_by_type = $request->query('order_by_type');
        $is_active = $request->get('is_active');
        $search_string = $request->query('search_string');
        $lang = $request->query('lang');

        $promo_list = Promotion::with( 'user')
            ->searchBy($search_string, $lang)
            ->searchByCreatedBy($created_by)
            ->orderByCustom($column, $order_by_type)
            ->isActive($is_active)
            ->paginate($paging);

        $promotions = new Collection($promo_list->items(), $this->promotionTransformer);
        $promotions->setPaginator(new IlluminatePaginatorAdapter($promo_list));
        $promotions = $this->fractal->createData($promotions);
        return BaseResponse::customResponse(
            'Get list successfully',
            $promotions->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $promotions->toArray()['meta']
        );

    }
}
