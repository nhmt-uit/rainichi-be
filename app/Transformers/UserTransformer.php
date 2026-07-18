<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\User;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{

    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'avatar' => filter_var($user->avatar, FILTER_VALIDATE_URL) ? $user->avatar : media_url_web($user->avatar),
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'level' => $user->level,
            'user_level' => $user->levelUser,
            'credits' => $user->credits,
            'type' => $user->type,
            'is_active' => $user->active,
            'user_info' => empty($user->infoUser) ? '' : (new UserInfoTransformer)->transform($user->infoUser),
            'created_at' => $user->created_at ? Carbon::parse($user->created_at)->format('d-m-Y') : null,
        ];
    }
}
