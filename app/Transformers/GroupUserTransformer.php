<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\GroupUser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class GroupUserTransformer extends TransformerAbstract
{
    /**
     * @var Manager
     */
    private $fractal;

    function __construct(Manager $fractal)
    {
        $this->fractal = $fractal;
    }

    public function transform(GroupUser $groupUser)
    {
        return [
            'id' => $groupUser->id,
            'name' => $groupUser->name,
            'created_by' => $groupUser->created_by,
            'updated_by' => $groupUser->updated_by,
            'status' => $groupUser->status
        ];
    }
}
