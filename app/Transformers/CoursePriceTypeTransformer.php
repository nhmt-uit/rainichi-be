<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;


use App\Models\CoursePriceType;
use League\Fractal\TransformerAbstract;

class CoursePriceTypeTransformer extends TransformerAbstract
{

    public function transform(CoursePriceType $courseType)
    {
        return [
            'id' => $courseType->id,
            'name' => $courseType->name,
            'duration' => $courseType->duration,
            'translations' => $courseType->getTranslationsArray()
        ];
    }
}
