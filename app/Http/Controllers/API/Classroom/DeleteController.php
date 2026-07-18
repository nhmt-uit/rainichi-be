<?php

namespace App\Http\Controllers\API\Classroom;

use App\Models\Classroom;
use App\Models\User;
use App\Service\BaseResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DeleteController extends Controller
{
    /**
     * Delete class by id.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $list_id = $request->list_id;
        if (isset($list_id) && in_array(Auth::user()->type, [User::LEADER, User::ADMIN])) {
            $except_id = [];
            $message = 'Deleting user class is successfully';
            foreach ($list_id as $id) {
                $classroom = Classroom::find($id);
                if ($classroom && ($classroom->courses->count() <= 0 || Auth::user()->type == User::ADMIN)) {
                    $classroom->delete();
                } else {
                    array_push($except_id, $id);
                }
            }
            if (sizeof($except_id)) {
                $message = $message . '.But some ids invalid with require';
            }
            return BaseResponse::customResponse(
                $message,
                $except_id,
                true,
                200,
                200,
                'Accepted'
            );
        } else {
            return BaseResponse::customResponse(
                'Fail to delete',
                [],
                false,
                Config('error_constant.vocabulary.delete_fail'),
                422,
                'Unprocessable Entity'
            );
        }
    }


}
