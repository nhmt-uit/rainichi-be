<?php

namespace App\Http\Controllers\API\Classroom\User;

use App\Models\UserClass;
use App\Mail;
use App\Service\BaseResponse;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UpdateController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        $class_course_id = $request->route('class_user_id');
        $data_change = $request->except('_method');
        $user_class = UserClass::query()->find($class_course_id);
        if ($user_class) {
            $data_change['updated_by'] = Auth::user()->id;
            $user_class->update($data_change);
            $user = $user_class->user;
            if ($user_class->is_active) {
                $user->notify(new Mail\AddUserToClassroom($user_class->classRoom));
            } else {
                $user->notify(new Mail\RemoveUserToClassroom($user_class->classRoom));
            }
            return BaseResponse::customResponse(
                'Update successfully',
                $user_class,
                true,
                200,
                200,
                'Success'
            );
        }
        return BaseResponse::customResponse(
            'Data not found',
            [],
            false,
            Config('error_constant.article.not_found'),
            404,
            'Not Found'
        );
    }
}
