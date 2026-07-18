<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;


use App\Models\Chapter;
use League\Fractal\TransformerAbstract;

class ChapterTransformer extends TransformerAbstract
{
    public function transform(Chapter $chapter)
    {
        return [
            'id' => $chapter->id,
            'type' => $chapter->type,
            'name' => $chapter->name,
            'image' => $chapter->image ? media_url_web( $chapter->image) : '',
            'translations' => $chapter->getTranslationsArray(),
        ];
    }
}
