<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\RewardLog;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class RewardTransformer extends TransformerAbstract
{

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var EventTransformer
     */
    private $eventTransformer;

    /**
     * @var RewardRuleTransformer
     */
    private $rewardRuleTransformer;

    function __construct(Manager $fractal, EventTransformer $eventTransformer, RewardRuleTransformer $rewardRuleTransformer)
    {
        $this->fractal = $fractal;
        $this->eventTransformer = $eventTransformer;
        $this->rewardRuleTransformer = $rewardRuleTransformer;
    }
    public function transform(RewardLog $rewardLog)
    {
        return [
            'credit' => $rewardLog->credit,
            'key' => $rewardLog->key,
            'reward' => $rewardLog->reward ? $this->fractal->createData(new Item($rewardLog->reward, $this->rewardRuleTransformer))->toArray()['data'] : null,
            'event' => $rewardLog->event ? $this->fractal->createData(new Item($rewardLog->event, $this->eventTransformer))->toArray()['data'] : null,
            'created_at' => convertAsiaDate($rewardLog->created_at),
        ];
    }
}
