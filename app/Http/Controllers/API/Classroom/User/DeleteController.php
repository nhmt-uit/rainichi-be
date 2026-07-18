<?php

namespace App\Http\Controllers\API\Classroom\User;

use App\Models\Classroom;
use App\Models\User;
use App\Service\BaseResponse;
use App\Service\ClassroomService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DeleteController extends Controller
{
    /**
     * Delete User Class
     * @param Request $request
     * @return  JsonResponse
     */
    public function deleteUserClass(Request $request)
    {
        $class_id = $request->route('id');
        $user_ids = $request->list_id;
        // In this case, We need to handle remove user out of theirs course that had assigned before
        if (isset($user_ids) && in_array(Auth::user()->type, [User::ADMIN, User::LEADER])) {
            $classroom = Classroom::query()->find($class_id);
            $users = User::query()->whereIn('id', $user_ids)->get();
            $classroom->users()->detach($users);
            ClassroomService::sendMailNotifyUserClass($users, $classroom, true);
        }
        return BaseResponse::customResponse(
            'Deleted user class success',
            [],
            true,
            200,
            202,
            'Accepted'
        );
    }
}
