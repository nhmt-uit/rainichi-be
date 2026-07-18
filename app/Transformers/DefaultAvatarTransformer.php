<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\DefaultAvatar;
use League\Fractal\TransformerAbstract;

class DefaultAvatarTransformer extends TransformerAbstract
{

    public function transform(DefaultAvatar $defaultAvatar)
    {
        return [
            'id' => $defaultAvatar->id,
            'avatar' => media_url_web( $defaultAvatar->avatar),
        ];
    }
}
