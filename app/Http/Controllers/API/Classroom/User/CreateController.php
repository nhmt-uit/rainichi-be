<?php

namespace App\Http\Controllers\API\Classroom\User;

use App\Models\Classroom;
use App\Models\User;
use App\Models\UserClass;
use App\Models\UserCompany;
use App\Service\BaseResponse;
use App\Service\ClassroomService;
use App\Service\UserClassService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;


class CreateController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addUserClass(Request $request)
    {
        $message = 'Creating user class is successfully';
        $user_ids = $request->get('user_ids');
        $classroom_id = $request->route('id');
        $is_allow_add = UserClassService::isAllowAddUser(count($user_ids), $classroom_id);
        if ($is_allow_add) {
            $users = User::query()->whereIn('id', $user_ids)->get();
            $classroom = Classroom::query()->find($classroom_id);
            if ($classroom && count($users)) {
                foreach ($users as $user) {
                    UserClass::query()->firstOrCreate([
                        'user_id' => $user->id,
                        'classroom_id' => $classroom_id
                    ], [
                        'user_id' => $user->id,
                        'classroom_id' => $classroom_id,
                        'is_active' => 1,
                        'created_by' => Auth::user()->id
                    ]);
                    UserCompany::query()->firstOrCreate([
                        'user_id' => $user->id,
                        'company_id' => $classroom->company_id
                    ], [
                        'user_id' => $user->id,
                        'company_id' => $classroom->company_id,
                        'is_active' => 1
                    ]);
                }
                ClassroomService::sendMailNotifyUserClass($users, $classroom);
            }
            return BaseResponse::customResponse(
                $message,
                [],
                true,
                201,
                201,
                'Created'
            );
        }
        return BaseResponse::customResponse(
            'Fail to insert user class. Number student is: ' . count($user_ids),
            [],
            false,
            Config('error_constant.normal.insert_fail'),
            500,
            'Something went wrong'
        );
    }
}
