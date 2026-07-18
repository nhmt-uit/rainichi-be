<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\RewardRule;
use League\Fractal\TransformerAbstract;

class RewardRuleTransformer extends TransformerAbstract
{

    public function transform(RewardRule $rewardRule)
    {
        return [
            'id' => $rewardRule->id,
            'credit' => $rewardRule->credit,
            'description' => $rewardRule->description,
            'method' => $rewardRule->method,
            'is_active' => $rewardRule->is_active,
            'start_date' => $rewardRule->start_date,
            'end_date' => $rewardRule->end_date,
            'translations' => $rewardRule->getTranslationsArray(),
        ];
    }
}
