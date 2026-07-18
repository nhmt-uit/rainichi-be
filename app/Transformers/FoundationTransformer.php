<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\Foundation;
use League\Fractal\TransformerAbstract;

class FoundationTransformer extends TransformerAbstract
{
    public function transform(Foundation $foundation)
    {
        return [
            'id' => $foundation->id,
            'translations' => $foundation->getTranslationsArray(),
        ];
    }
}
