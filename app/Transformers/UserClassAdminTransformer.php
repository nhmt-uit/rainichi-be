<?php

namespace App\Transformers;

use App\Models\CourseClass;
use App\Models\UserClass;
use Carbon\Carbon;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class UserClassAdminTransformer extends TransformerAbstract
{

    public function transform(UserClass $userClass)
    {
        return [
            'id' => $userClass->id,
            'classroom_id' => $userClass->classroom_id,
            'avatar' => $userClass->user ? media_url_web($userClass->user->avatar) : null,
            'user_id' => $userClass->user->id ?? null,
            'name' => $userClass->user->name ?? null,
            'email' => $userClass->user->email ?? null,
            'phone' => $userClass->user->phone ?? null,
            'is_active' => $userClass->is_active ,
            'created_at' => $userClass->created_at ? Carbon::parse($userClass->created_at)->format('d-m-Y') : null,
            'updated_at' => $userClass->updated_at ? Carbon::parse($userClass->updated_at)->format('d-m-Y') : null,
            'created_by' => $userClass->createdBy->name ?? null,
            'updated_by' => $userClass->updatedBy->name ?? null,
        ];
    }
}
