<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\CourseRoute;
use League\Fractal\TransformerAbstract;

class CourseRouteTransformer extends TransformerAbstract
{

    public function transform(CourseRoute $coursePrice)
    {
        return [
            'id' => $coursePrice->id,
            'durations' => $coursePrice->durations,
            'translations' => $coursePrice->getTranslationsArray(),
        ];
    }
}
