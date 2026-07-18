<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\Level;
use League\Fractal\TransformerAbstract;

class LevelTransformer extends TransformerAbstract
{

    public function transform(Level $level)
    {
        return [
            'id' => $level->id,
            'is_foundation' => $level->is_foundation,
            'name' => $level->name,
            'translations' => $level->getTranslationsArray(),
        ];
    }
}
