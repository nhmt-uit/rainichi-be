<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 07/03/2019
 * Time: 13:19
 */

namespace App\Http\Controllers\API\Payment;


use App\Http\Controllers\Controller;
use App\Models\ExerciseSubmit;
use App\Models\Order;
use App\Models\PurchasedCourse;
use App\Models\RewardLog;
use App\Models\TestResult;
use App\Models\User;
use App\Service\BaseResponse;
use App\Service\RewardRules;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BuyingController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function buyCourse(Request $request)
    {
        $user = Auth::user();
        $order_data = $request->all();
        if ($user->credits < $order_data['buying_credits']) {
            return BaseResponse::customResponse(
                'Số credit không đủ để thanh toán - Insufficient fund',
                [],
                false,
                422,
                422,
                'Insufficient fund',
                []
            );
        } else {
            $order = null;
            $buy_type = $request->segment(3);
            switch ($buy_type) {
                case 'course':
                    $order = Order::query()->where('user_id', $user->id)->where('course_id', $order_data['course_id'])->first();
                    break;
                case 'test':
                    $order = Order::query()->where('user_id', $user->id)->where('test_id', $order_data['test_id'])->first();
                    break;
            }
            if ($order) {
                if ($buy_type === 'course') {
                    $purchased_data = PurchasedCourse::query()->where('user_id', $user->id)->where('course_id', $order_data['course_id'])->first();
                    if ($order_data['duration'] != 0) {
                        if ($purchased_data->end_date != null)
                            $purchased_data->end_date = Carbon::parse($purchased_data->end_date)->addMonth($order_data['duration']);
                    } else {
                        $purchased_data->end_date = null;
                    }
                    $purchased_data->save();
                } else {
                    return BaseResponse::customResponse(
                        'This exam has already been bought',
                        [],
                        true,
                        410,
                        410,
                        'Resource Unavailable',
                        []
                    );
                }
            } else {
                $purchased_data['start_date'] = Carbon::now();
                if ($order_data['duration'] != 0) {
                    $purchased_data['end_date'] = Carbon::now()->addMonth($order_data['duration']);
                }
                $purchased_data['user_id'] = $user->id;
                if ($buy_type === 'course') {
                    $purchased_data['course_id'] = $order_data['course_id'];
                } else {
                    $purchased_data['test_id'] = $order_data['test_id'];
                }
                PurchasedCourse::query()->create(
                    $purchased_data
                );

                // Remove test result before buy new test.
                if (array_key_exists('test_id', $order_data)) {
                    TestResult::query()->where('test_id', $order_data['test_id'])->where('user_id', $user->id)->delete();
                }
                if (array_key_exists('course_id', $order_data)) {
                    ExerciseSubmit::query()->where('course_id', $order_data['course_id'])->where('user_id', $user->id)->delete();
                }

                $order_data['user_id'] = $user->id;
                $order_data['status'] = Order::DONE;
                Order::query()->create($order_data);
            }

            $user->credits -= $order_data['buying_credits'] - $order_data['reward_credits'];
            if ($user->save()) {
                if ($order_data['reward_credits'] > 0) {
                    RewardLog::query()->create([
                        'user_id' => $user->id,
                        'key' => config('reward_constant.buy'),
                        'credit' => $order_data['reward_credits']
                    ]);
                }
                return BaseResponse::customResponse(
                    'Success',
                    [],
                    true,
                    200,
                    200,
                    'Success',
                    []
                );
            }
        }
    }
}
