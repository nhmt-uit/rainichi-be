<?php

namespace App\Http\Controllers\API\Classroom;

use App\Http\Requests\ClassroomRequest;
use App\Models\Classroom;
use App\Models\User;
use App\Service\BaseResponse;
use App\Transformers\ClassroomAdminTransformer;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{
    /**
     * @param ClassroomRequest $request
     * @return JsonResponse
     * @throws Exception $exception
     */
    public function create(ClassroomRequest $request)
    {
        try {
            $classroom_data = $request->all();
            $classroom_data['created_by'] = Auth::user()->id ?? null;
            $classroom_data['credits'] = 0;
            $classroom = Classroom::create($classroom_data);

            // In this case handle create user class -- model UserClass
            if (key_exists('teacher_id', $classroom_data)) {
                $user = User::query()->find($classroom_data['teacher_id']);
                $classroom->users()->attach($user);
            }

            return BaseResponse::customResponse(
                'Create classroom successfully',
                (new ClassroomAdminTransformer)->transform($classroom),
                true,
                201,
                201,
                'Created'
            );
        } catch (Exception $e) {
            return BaseResponse::customResponse(
                'Fail to insert new classroom' . $e->getMessage(),
                [],
                false,
                Config('error_constant.normal.insert_fail'),
                500,
                'Unprocessable Entity'
            );
        }
    }
}
