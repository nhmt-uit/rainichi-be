<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\UserClass;
use League\Fractal\TransformerAbstract;

class UserClassTransformer extends TransformerAbstract
{

    public function transform(UserClass $userClass)
    {
        $user = $userClass->user;
        return [
            'id' => $user->id,
            'avatar' => filter_var($user->avatar, FILTER_VALIDATE_URL) ? $user->avatar : media_url_web($user->avatar),
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone
        ];
    }
}
