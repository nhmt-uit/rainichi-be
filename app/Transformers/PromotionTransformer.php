<?php

namespace App\Transformers;

use App\Models\Promotion;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class PromotionTransformer extends TransformerAbstract
{
    /**
     * @param Promotion $promotion
     * @return array
     */
    public function transform(Promotion $promotion)
    {
        return [
            'id' => $promotion->id,
            'translations' => $promotion->getTranslationsArray(),
            'is_active' => $promotion->is_active,
            'created_at' => Carbon::parse($promotion->created_at)->format('d-m-Y'),
            'created_by' => $promotion->user ? $promotion->user->name : ''
        ];
    }
}
