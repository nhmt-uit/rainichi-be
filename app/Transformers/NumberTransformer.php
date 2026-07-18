<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\Number;
use League\Fractal\TransformerAbstract;

class NumberTransformer extends TransformerAbstract
{
    public function transform(Number $number)
    {
        return [
            'number' => $number->number,
            'instance' => $number->instance,
            'audio' => media_url_web( $number->audio),
        ];
    }
}
