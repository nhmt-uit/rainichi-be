<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\User;


use App\Http\Controllers\Controller;
use App\Mail\AddUserToClassroom;
use App\Models\Company;
use App\Models\DefaultAvatar;
use App\Models\User;
use App\Models\UserInfo;
use App\Notifications\VerificationEmail;
use App\Service\BaseResponse;
use App\Service\RandomAvatar;
use App\Service\UploadService;
use App\Service\UserClassService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CreateController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email|unique:users',
        ]);
        $data = $request->all();
        if ($validator->fails()) {
            $user = User::query()->where('email', $request->get('email'))->first();
            $user->avatar = filter_var($user->avatar, FILTER_VALIDATE_URL) ? $user->avatar : media_url_web($user->avatar);
            return response()->json([
                "message" => "This email has already been taken",
                'data' => $user,
                "errors" => [
                    "email" => [
                        "validation.unique"
                    ]
                ]
            ])->setStatusCode(422);
        } elseif (key_exists('classroom_id', $data) && !UserClassService::isAllowAddUser(1, $data['classroom_id'])) {
            return BaseResponse::customResponse(
                "Current class can't add more student",
                [],
                false,
                500,
                500,
                "Internal Error",
                []
            );
        } else {
            if ($request->hasFile('avatar')) {
                $avatar = UploadService::handleUploadFile($request->file('avatar'), Config('uploadpath.avatar_folder'));
                $data['avatar'] = $avatar;
            } else {
                $data['avatar'] = RandomAvatar::random();
            }
            $data['password'] = bcrypt($request->get('password'));
            $data['activation_token'] = sha1(time());
            $data['active'] = $request->get('active') ?? 0;
            $user = User::query()->create($data);

            #create more info for teacher
            if ((int)$data['type'] === User::TEACHER) {
                $data['user_id'] = $user->id;
                $userInfo = UserInfo::createOrUpdate($data, $user->id);
                $language_keys = array_keys($data['translations']);
                foreach ($language_keys as $language) {
                    $userInfo->translateOrNew($language)->content = $data['translations'][$language]['content'] ?? '';
                    $userInfo->translateOrNew($language)->diploma = $data['translations'][$language]['diploma'] ?? '';
                    $userInfo->translateOrNew($language)->major = $data['translations'][$language]['major'] ?? '';
                }
                $userInfo->save();
            }

            #assige new user to company & classroom
            if ((int)$data['type'] === User::ENTERPRISE &&
                key_exists('company_id', $data) && key_exists('classroom_id', $data)) {
                $company = Company::query()->find($data['company_id']);
                $classRoom = $company->classRoom()->where('id', $data['classroom_id'])->first();
                try {
                    $company->users()->attach($user);
                    $classRoom->users()->attach($user);
                    Mail::to($user)->queue(new AddUserToClassroom($classRoom));
                } catch (QueryException  $e) {
                    Log::error('Something went wrong when try to add user to company: ' . $e->getMessage());
                }
            }
            $user->notify(new VerificationEmail($request->get('password')));
            if ($user) {
                return BaseResponse::customResponse(
                    'Successful',
                    $user,
                    true,
                    200,
                    201,
                    "Created",
                    []
                );
            } else {
                return BaseResponse::customResponse(
                    'Fail to create user',
                    [],
                    false,
                    422,
                    422,
                    "Unprocessable Entity",
                    []
                );
            }
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createDefaultAvatar(Request $request)
    {
        if ($request->hasFile('avatar')) {
            $avatar = new DefaultAvatar();
            $avatar_url = UploadService::handleUploadFile($request->file('avatar'), Config('uploadpath.avatar_default_folder'));
            $avatar->avatar = $avatar_url;
            $avatar->created_by = Auth::user()->id;
            if ($avatar->save()) {
                return BaseResponse::customResponse(
                    'Create successfully',
                    $avatar,
                    true,
                    200,
                    200,
                    'Success'
                );
            }
        }
    }
}
