<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;


use App\Models\Language;
use League\Fractal\TransformerAbstract;

class LanguageTransformer extends TransformerAbstract
{

    public function transform(Language $language)
    {
        return [
            'id' => $language->id,
            'name' => $language->name,
            'code' => $language->code,
            'flag' => media_url_web( $language->flag),
            'is_default' => $language->is_default,
        ];
    }
}
