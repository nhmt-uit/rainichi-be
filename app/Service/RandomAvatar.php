<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 17/12/2018
 * Time: 11:22
 */

namespace App\Service;


use App\Models\DefaultAvatar;

class RandomAvatar
{
    /**
     * Random avatar after sign up successful.
     * @return mixed
     */
    public static function random()
    {
        $avatar_default = DefaultAvatar::pluck('avatar')->toArray();
        $avatar_random = array_rand($avatar_default, 1);
        return $avatar_default[$avatar_random];
    }
}
