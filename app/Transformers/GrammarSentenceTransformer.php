<?php
/**
 * Created by PhpStorm.
 * User: tu.duong
 * Date: 08/01/2019
 * Time: 02:55:47
 */

namespace App\Transformers;

use App\Models\GrammarSentence;
use League\Fractal\TransformerAbstract;

class GrammarSentenceTransformer extends TransformerAbstract
{

    public function transform(GrammarSentence $sentence)
    {
        return [
            'id' => $sentence->id,
            'is_active' => $sentence->is_active,
            'sort_order' => $sentence->sort_order,
            'translations' => $sentence->getTranslationsArray()
        ];
    }
}
