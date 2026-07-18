<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\UserInfo;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class UserInfoTransformer extends TransformerAbstract
{

    public function transform(UserInfo $info)
    {
        return [
            'id' => $info->id,
            'country_id' => $info->country_id,
            'facebook' => $info->facebook,
            'google_plus' => $info->google_plus,
            'twitter' => $info->twitter,
            'instagram' => $info->instagram,
            'translations' => $info->getTranslationsArray(),
        ];
    }
}
