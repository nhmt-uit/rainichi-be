<?php

namespace App\Transformers;

use App\Models\CoursePriceCurrency;
use League\Fractal\TransformerAbstract;

class CoursePriceCurrencyTransformer extends TransformerAbstract
{

    public function transform(CoursePriceCurrency $priceCurrency)
    {
        return [
            'id' => $priceCurrency->id,
            'currency' => $priceCurrency->currency,
        ];
    }
}
