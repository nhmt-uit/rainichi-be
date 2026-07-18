<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 14:00
 */

namespace App\Http\Controllers\API\User;


use App\Http\Controllers\Controller;
use App\Models\PurchasedCourse;
use App\Models\User;
use App\Models\UserInfo;
use App\Service\BaseResponse;
use App\Service\UploadService;
use App\Transformers\UserInfoTransformer;
use App\Transformers\UserTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UpdateController extends Controller
{
    const ACTIVE = true;

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        if (in_array(Auth::user()->type, [User::ADMIN, User::LEADER])) {
            $user = User::query()->find($request->route('id'));
            if ($user) {
                $data_change = $request->except('_method');
                if ($request->hasFile('avatar')) {
                    $old_avatar = $user->avatar;
                    $avatar = UploadService::handleUploadFile($request->file('avatar'), Config('uploadpath.avatar_folder'));
                    $data_change['avatar'] = $avatar;
                    if ($old_avatar != null && !strpos($old_avatar, 'users/default-avatars') === 0) {
                        UploadService::handleRemoveFile($old_avatar);
                    }
                }
                if ($request->has('avatar_default')) {
                    $old_avatar = $user->avatar;
                    $data_change['avatar'] = explode(url('storage/') . '/', $request->get('avatar_default'))[1];
                    // remove old avatar
                    if ($old_avatar != null && strpos($old_avatar, 'users/default-avatars') === false) {
                        UploadService::handleRemoveFile($old_avatar);
                    }
                }
                if ($request->has('password')) {
                    $data_change['password'] = bcrypt($data_change['password']);
                }
                $user->update($data_change);

                #create more info for teacher
                if ((int)$user->type === User::TEACHER) {
                    $userInfo = UserInfo::createOrUpdate($data_change, $user->id);
                    // get translation keys and add to translation table
                    if (array_key_exists('translations', $data_change)) {
                        $language_keys = array_keys($data_change['translations']);
                        foreach ($language_keys as $language) {
                            $userInfo->translateOrNew($language)->content = $data_change['translations'][$language]['content'];
                            $userInfo->translateOrNew($language)->diploma = $data_change['translations'][$language]['diploma'];
                            $userInfo->translateOrNew($language)->major = $data_change['translations'][$language]['major'];
                        }
                        $userInfo->save();
                    }
                }
                if ($user->save()) {
                    $user->avatar = media_url_web($user->avatar);
                    return BaseResponse::customResponse(
                        'Successfully update user profile',
                        $user,
                        true,
                        200,
                        200,
                        'Success'
                    );
                }
            } else {
                return BaseResponse::customResponse(
                    'User not found',
                    [],
                    false,
                    404,
                    404,
                    'NotFound',
                    []
                );
            }
        } else {
            return BaseResponse::customResponse(
                'You dont have permission to view this profile.',
                [],
                false,
                403,
                403,
                'PERMISSION DENIED',
                []
            );
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail($id)
    {
        if ((in_array(Auth::user()->type, [User::ADMIN, User::LEADER]))) {
            $user = User::query()->with('infoUser', 'applyUser')->find($id);
            if ($user) {
                $user->avatar = filter_var($user->avatar, FILTER_VALIDATE_URL) ? $user->avatar : media_url_web($user->avatar);
                $userData = (new UserTransformer)->transform($user);
                $userData['info_user'] = empty($user->infoUser) ? '' : (new UserInfoTransformer)->transform($user->infoUser);
                return BaseResponse::customResponse(
                    'Successfully get user profile',
                    $userData,
                    true,
                    200,
                    200,
                    'Success'
                );
            } else {
                return BaseResponse::customResponse(
                    'User not found',
                    [],
                    false,
                    404,
                    404,
                    'NotFound',
                    []
                );
            }
        } else {
            return BaseResponse::customResponse(
                'You dont have permission to view this profile.',
                [],
                false,
                403,
                403,
                'PERMISSION DENIED',
                []
            );
        }
    }

    /**
     * @param Request $request
     * @apiParam {Number} course_id Course unique id
     * @apiParam {Number} user_id User unique id
     * @apiParam {Number} month Course durations
     * @apiParam {Boolean} status Active: true, In-active: true
     * @return \Illuminate\Http\JsonResponse
     * @apiSuccess {Number} course_id Course unique id
     * @apiSuccess {Number} user_id User unique id
     * @apiSuccess {Number} month Course durations
     * @apiSuccess {Boolean} status Active: true, In-active: true
     * @apiSuccess {Number} id Purchase course unique id
     * @apiSuccessExample {json} Success-Response:
     * HTTP/1.1 200 OK
     *  {
     *    "success": true,
     *    "message": "Success",
     *    "data": {
     *    "course_id": 3,
     *    "user_id": 1,
     *    "start_date": {
     *    "date": "2019-08-22 18:40:27.816840",
     *    "timezone_type": 3,
     *    "timezone": "UTC"
     *    },
     *    "end_date": {
     *    "date": "2020-02-22 18:40:27.816864",
     *    "timezone_type": 3,
     *    "timezone": "UTC"
     *    },
     *    "id": 2
     *    },
     *    "meta": [],
     *    "code": 200
     *  }
     * @example
     * {
     *    "course_id": 1,
     *    "test_id": '',
     *    "user_id": 1,
     *    "month" : 6,
     *    "status": true
     *  }
     * OR
     * {
     *    "course_id": '',
     *    "test_id": 1,
     *    "user_id": 1,
     *    "month" : 6,
     *    "status": true
     *  }
     * @api {post} /active-course Active or In-active course for user ( make by admin).
     * @apiName activeOrInActiveCourse
     * @apiGroup User
     */
    public function activeOrInActiveCourse(Request $request)
    {
        // remove un-using data into data model.
        $data = $request->except(['status', 'duration']);
        $duration = $request->get('duration');
        // Check status active or in-active
        if ($request->get('status') == self::ACTIVE) {
            // Course start from now
            $data['start_date'] = Carbon::now();
            // Course end after (n) months admin input.
            $is_new = true;
            if (key_exists('course_id', $data)) {
                $purchase = PurchasedCourse::query()
                    ->where('user_id', $data['user_id'])
                    ->where('course_id', $data['course_id'])->orderByDesc('id')->first();
                if ($purchase) {
                    $end_date = $duration != 0 ? Carbon::parse($purchase->end_date)->addMonths($duration) : null;
                    $purchase->update(array('end_date' => $end_date));
                    $is_new = false;
                }
            }
            if ($is_new) {
                $data['end_date'] = $duration != 0 ? Carbon::now()->addMonth($duration) : null;
                $purchase = PurchasedCourse::query()->create($data);
            }
        } else {
            // Find out course before update in-active status.
            $purchase = PurchasedCourse::query()
                ->where('user_id', $data['user_id'])
                ->where(function ($q) use ($data) {
                    if (key_exists('course_id', $data)) {
                        $q->where('course_id', $data['course_id']);
                    } else {
                        $q->where('test_id', $data['test_id']);
                    }
                });
            if ($purchase->count() > 0) {
                // Set course's end-date in this day. Stop user access anymore.
                $end_date = Carbon::now()->subDay();
                $purchase->update(array('end_date' => $end_date));
            }
        }
        /* This function just active or in-active course for user, i have not update order log or something like that yet.
           Continue here if need longer.
        */
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
