<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;


use App\Models\Alphabet;
use League\Fractal\TransformerAbstract;

class AlphabetTransformer extends TransformerAbstract
{
    public function transform(Alphabet $alphabet)
    {
        return [
            'id' => $alphabet->id,
            'character' => $alphabet->character,
            'image' => url('storage/'.$alphabet->image),
            'audio' => url('storage/'.$alphabet->audio),
            'translations' => $alphabet->getTranslationsArray(),
        ];
    }
}
