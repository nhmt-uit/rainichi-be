<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 3/7/20
 * Time: 10:38
 */

namespace App\Service;


use App\Jobs\FCMJob;
use App\Models\RewardLog;
use App\Models\RewardRule;
use App\Models\User;
use App\Models\UserTokenDevice;
use Carbon\Carbon;

class RewardRules
{

    protected $user;

    protected $key;

    function __construct($user, $key = null)
    {
        $this->user = $user;
        $this->key = $key;
    }

    /**
     * Login reward
     * @param $id
     */
    public function whenLogin()
    {
        $reward = null;
        $key = null;
        // If users login continuously within 7 days will be rewarded.
        if (Carbon::now()->diffInDays($this->user->lass_access) == 1) {
            if ($this->user->count_access_time == 6) {
                $key = config('reward_constant.login_7');
                $reward = $this->findRewardRule($key);
            } else {
                $key = config('reward_constant.login');
                $reward = $this->findRewardRule($key);
            }
        }
        if ($reward) {
            $this->user->credits += $reward->credit;
            $this->user->lass_access = Carbon::now();
            if ($this->user->count_access_time == 7) {
                $this->user->count_access_time = 1;
            } else {
                $this->user->count_access_time += 1;
            }
            if ($this->user->save()) {
                RewardLog::query()->create([
                    'user_id' => $this->user->id,
                    'credit' => $reward->credit,
                    'key' => $key
                ]);
            }
        }
    }

    /**
     * Add reward log by key
     * @param $id
     * @param $key
     */
    public function add()
    {
        // If users login continuously within 7 days will be rewarded.
        $reward = $this->findRewardRule($this->key);
        if (!$reward) {
            return;
        }
        $this->user->credits += $reward->credit;
        if ($this->user->save()) {
            RewardLog::query()->create([
                'user_id' => $this->user->id,
                'credit' => $reward->credit,
                'key' => $this->key
            ]);
            $tokens = UserTokenDevice::query()->where('user_id', $this->user->id)->pluck('token_device');
            dispatch(new FCMJob($tokens, "Bạn vừa được nhận $reward->credit xu từ Rainichi", $reward->description));
        }

    }

    /**
     * Find reward rule by key.
     * @param $key
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|null|object
     */
    private function findRewardRule($key)
    {
        $reward = RewardRule::query()->where('route', $key)
            ->whereDate('start_date', '<=', Carbon::now())
            ->whereDate('end_date', '>=', Carbon::now())
            ->first();
        return $reward;
    }
}
