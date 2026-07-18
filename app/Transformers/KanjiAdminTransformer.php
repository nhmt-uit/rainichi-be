<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\Kanji;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class KanjiAdminTransformer extends TransformerAbstract
{

    public function transform(Kanji $kanji)
    {
        return [
            'id' => $kanji->id,
            'kanji' => $kanji->kanji,
            'meaning' => $kanji->meaning,
            'image' => $kanji->image ? $kanji->image : null,
            'audio' => $kanji->audio ? media_url_web( $kanji->audio) : null,
            'translations' => $kanji->getTranslationsArray(),
            'is_active' => $kanji->is_active,
            'level_id' => $kanji->level_id ? $kanji->level_id : 0,
            'level' => $kanji->levels ? $kanji->levels->name : null,
            'created_at' => Carbon::parse($kanji->created_at)->format('d-m-Y'),
            'created_by' => $kanji->user ? $kanji->user->name : null,
        ];
    }
}
