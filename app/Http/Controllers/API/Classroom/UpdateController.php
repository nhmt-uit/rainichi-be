<?php

namespace App\Http\Controllers\API\Classroom;

use App\Models\Classroom;
use App\Models\Company;
use App\Models\User;
use App\Models\UserClass;
use App\Service\BaseResponse;
use App\Transformers\ClassroomAdminTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UpdateController extends Controller
{
    /**
     * Get company details by id.
     * @param Request $request
     * @return JsonResponse
     */

    public function detail(Request $request)
    {
        $classroom_id = $request->route('id');
        $classroom = Classroom::query()->with('user')->find($classroom_id);
        if ($classroom) {
            return BaseResponse::customResponse(
                'Get data successfully',
                (new ClassroomAdminTransformer)->transform(($classroom)),
                true,
                200,
                200,
                'Success'
            );
        } else {
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

    /**
     * Get company details by id.
     * @param Request $request
     * @return JsonResponse
     */

    public function update(Request $request)
    {
        $classroom_id = $request->route('id');
        $data_change = $request->except('_method');
        $classroom = Classroom::query()->find($classroom_id);
        if ($classroom) {
            $data_change['updated_by'] = Auth::user()->id;

            // Only Super Admin can edit is_approved enable classroom
            if (key_exists('is_approved', $data_change) && Auth::user()->type <> User::ADMIN) {
                // not allow update approved if not Admin system
                unset($data_change['is_approved']);
            }

            // If this classroom have any course that can't update number_of_students
            if (key_exists('num_of_employee', $data_change) && $classroom->courses->count() > 0
                && Auth::user()->type <> User::ADMIN) {
                // not allow update approved if not Admin system
                unset($data_change['num_of_employee']);
            }

            //not allow update course_id
            if (key_exists('course_id', $data_change)) {
                unset($data_change['course_id']);
            }
            // update teacher id
            if (key_exists('teacher_id', $data_change) && Auth::user()->type == User::ADMIN) {
                if($classroom->teacher()){
                    $classroom->users()->detach($classroom->teacher());
                }
                $classroom->users()->attach($data_change['teacher_id']);
            }

            if ($classroom->update($data_change)) {
                return BaseResponse::customResponse(
                    'Update classroom successfully',
                    (new ClassroomAdminTransformer)->transform($classroom),
                    true,
                    200,
                    200,
                    'Success'
                );
            }
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

    /**
     * @return JsonResponse
     */
    public function checkedExistClass()
    {
        $user = Auth::user();
        $company = Company::getCompanyByUser($user->id);
        $num_class = $company ? $company->classRoom->count() : 0;
        $user_class = UserClass::query()->where('user_id', $user->id)->count();
        if ($user_class > 0 || $user->isAdmin()) {
            return BaseResponse::customResponse(
                'You dont have permission to create classroom',
                $num_class,
                false,
                403,
                403,
                'Forbidden'
            );
        } elseif ($num_class > 0) {
            return BaseResponse::customResponse(
                'You has one or more classroom ',
                $num_class,
                false,
                409,
                409,
                'Conflict'
            );
        }
        return BaseResponse::customResponse(
            'User can buy class',
            [],
            true,
            200,
            200,
            'Success'
        );
    }
}
