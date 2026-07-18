<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;


use App\Models\CoursePrice;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class CoursePriceTransformer extends TransformerAbstract
{

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var CoursePriceTypeTransformer
     */
    private $coursePriceType;

    /**
     * @ $coursePriceCurrency
     */
    private $coursePriceCurrency;

    function __construct(Manager $fractal, CoursePriceTypeTransformer $coursePriceType, CoursePriceCurrencyTransformer $coursePriceCurrency)
    {
        $this->fractal = $fractal;
        $this->coursePriceType = $coursePriceType;
        $this->coursePriceCurrency = $coursePriceCurrency;
    }

    public function transform(CoursePrice $coursePrice)
    {
        return [
            'id' => $coursePrice->id,
            'customer_type_id' => $coursePrice->customer_type_id,
            'buying_credits' => $coursePrice->buying_credits,
            'reward_credits' => $coursePrice->reward_credits,
            'discount_credits' => $coursePrice->discount_credits,
            'landing_buy_credit' => $coursePrice->landing_buy_credit,
            'landing_discount_credit' => $coursePrice->landing_discount_credit,
            'landing_reward_credit' => $coursePrice->landing_reward_credit,
            'is_active' => $coursePrice->is_active,
            'price_types' => $this->fractal->createData(new Item($coursePrice->priceType, $this->coursePriceType))->toArray()['data'],
            'currency' => $coursePrice->currency ? $coursePrice->currency->currency : null
        ];
    }
}
