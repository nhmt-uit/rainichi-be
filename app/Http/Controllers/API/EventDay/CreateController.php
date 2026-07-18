<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 3/18/20
 * Time: 21:21
 */

namespace App\Http\Controllers\API\EventDay;


use App\Http\Controllers\Controller;
use App\Models\EventDay;
use App\Models\ListUserByGroupUser;
use App\Models\RewardLog;
use App\Models\User;
use App\Service\BaseResponse;
use App\Service\RewardRules;
use App\Transformers\EventTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;

class CreateController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var EventTransformer
     */
    private $eventTransformer;

    function __construct(Manager $fractal, EventTransformer $eventTransformer)
    {
        $this->fractal = $fractal;
        $this->eventTransformer = $eventTransformer;
    }

    public function index(Request $request)
    {

        $event_data = $request->all();
        $type = $event_data['type'];
        $eventDay = EventDay::create($event_data);

        // get translation keys and add to translation table
        $language_keys = array_keys($event_data['translations']);
        foreach ($language_keys as $language) {
            $eventDay->translateOrNew($language)->name = $event_data['translations'][$language]['name'];
        }
        $eventDay->users()->sync($event_data['user_ids']);
        if ($type === EventDay::USER_SELECTED) {
            self::addRewardLog($event_data['user_ids'], $event_data['credits'], $eventDay->id);
        } else if ($type === EventDay::GROUP) {
            $group_id = $event_data['group_id'];
            $userList = ListUserByGroupUser::query()->where('group_user_id', $group_id)->pluck('user_id');
            self::addRewardLog($userList, $event_data['credits'], $eventDay->id);
        } else {
            DB::table('users')->increment('credits', $event_data['credits']);
            $userList = User::query()
                ->where('active', true)
                ->whereIn('type', [User::ENTERPRISE, User::USER])
                ->pluck('id');
            self::addRewardLog($userList, $event_data['credits'], $eventDay->id);
        }
        if ($eventDay->save()) {
            return BaseResponse::customResponse(
                'Create successfully',
                $eventDay,
                true,
                200,
                201,
                'Created'
            );
        } else {
            return BaseResponse::customResponse(
                'Fail to insert',
                [],
                false,
                Config('error_constant.vocabulary.insert_fail'),
                422,
                'Unprocessable Entity'
            );
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function list()
    {
        $events = EventDay::query()->with('users')->orderByDesc('id')->get();
        $events = new Collection($events, $this->eventTransformer);
        $events = $this->fractal->createData($events);
        return BaseResponse::customResponse(
            'Get list successfully',
            $events->toArray()['data'],
            true,
            200,
            200,
            'Success'
        );
    }

    /**
     * @param $user_ids
     * @param $credit
     * @param $event_id
     */
    function addRewardLog($user_ids, $credit, $event_id)
    {
        $rewardLog = [];
        foreach ($user_ids as $user_id) {
            $user = User::query()->find($user_id);
            if ($user) {
                $user->credits += $credit;
                $user->save();
                array_push($rewardLog, [
                    'user_id' => $user->id,
                    'credit' => $credit,
                    'key' => config('reward_constant.event'),
                    'event_id' => $event_id,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ]);
            }
        }
        RewardLog::query()->insert($rewardLog);
    }
}
