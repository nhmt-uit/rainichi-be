<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\ListUserByGroupUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class ListUserByGroupUserTransformer extends TransformerAbstract
{
    /**
     * @var Manager
     */
    private $fractal;

    function __construct(Manager $fractal)
    {
        $this->fractal = $fractal;
    }

    public function transform(ListUserByGroupUser $listUserByGroupUser)
    {
        return [
            'id' => $listUserByGroupUser->id,
            'group_user_id' => $listUserByGroupUser->group_user_id,
            'avatar' => $listUserByGroupUser->user ? media_url_web($listUserByGroupUser->user->avatar) : null,
            'user_id' => $listUserByGroupUser->user->id ?? null,
            'name' => $listUserByGroupUser->user->name ?? null,
            'email' => $listUserByGroupUser->user->email ?? null,
            'phone' => $listUserByGroupUser->user->phone ?? null,
            'created_at' => $listUserByGroupUser->created_at ? Carbon::parse($listUserByGroupUser->created_at)->format('d-m-Y') : null
        ];
    }
}
