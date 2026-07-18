<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 3/18/20
 * Time: 22:20
 */

namespace App\Http\Controllers\API\Share;

use App\Models\RewardLog;
use App\Models\User;
use App\Service\BaseResponse;
use App\Service\RewardRules;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class CreateController
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        $reward_log = RewardLog::query()->where('key', config('reward_constant.share'))->whereDate('created_at', Carbon::now())->first();
        if ($reward_log) {
            return BaseResponse::customResponse(
                'Duplicated action',
                [],
                false,
                409,
                409,
                'Conflict'
            );
        } else {
            // Add reward for user
            $user = User::query()->find(Auth::user()->id);
            if ($user) {
                $rewardRule = new RewardRules($user, config('reward_constant.share'));
                $rewardRule->add();
                return BaseResponse::customResponse(
                    'Share successfully ',
                    [],
                    true,
                    200,
                    200,
                    'Success'
                );
            } else {
                return BaseResponse::customResponse(
                    'User not found',
                    [],
                    fasle,
                    404,
                    404,
                    'NotFound'
                );
            }


        }

    }
}