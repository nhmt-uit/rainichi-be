<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\Vocabulary;
use League\Fractal\TransformerAbstract;

class VocabularyTransformer extends TransformerAbstract
{

    public function transform(Vocabulary $vocabulary)
    {
        return [
            'id' => $vocabulary->id,
            'vocabulary' => $vocabulary->vocabulary,
            'image' => $vocabulary->image ? media_url_web( $vocabulary->image) : null,
            'audio' => $vocabulary->audio ? media_url_web( $vocabulary->audio) : null,
            'audio_example_1' => $vocabulary->audio_example_1,
            'audio_example_2' => $vocabulary->audio_example_2,
            'hiragana' => $vocabulary->hiragana,
            'kanji' => $vocabulary->kanji,
            'katakana' => $vocabulary->katakana,
            'spelling' => $vocabulary->spelling,
            'translations' => $vocabulary->getTranslationsArray(),
            'is_active' => $vocabulary->is_active,
            'svg' => $vocabulary->svg,
            'type' => $vocabulary->type,
        ];
    }
}
