<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Jobs\Register;
use App\Models\Company;
use App\Models\UserClass;
use App\Service\BaseResponse;
use App\Service\InitLevel;
use App\Service\RandomAvatar;
use App\Service\RewardRules;
use App\Service\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Notifications\SignupActivate;
use App\Models\UserTokenDevice;
use GuzzleHttp\Client;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sign_up(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);
        if ($validator->fails()) {

            return BaseResponse::customResponse(
                $validator->errors()->first(),
                [],
                false,
                Config('error_constant.auth.email_duplicate'),
                422,
                'Unprocessable Entity'
            );
        }
        $user = User::query()->where('email', $request->email)->first();
        if ($user && $user->active === true) {
            return BaseResponse::customResponse(
                'Email has already been taken',
                [],
                false,
                Config('error_constant.auth.email_duplicate'),
                422,
                'Unprocessable Entity'
            );
        } else if ($user && !$user->active) {
            $user->activation_token = mt_rand(100000, 999999);
            $user->save();
            dispatch(new Register($user->email, $user->activation_token));
            return BaseResponse::customResponse(
                'Waiting for active',
                [],
                false,
                401,
                401,
                'Unauthorized'
            );
        } else {
            $user = new User([
                'email' => $request->email,
                'type' => User::USER,
                'activation_token' => mt_rand(100000, 999999)
            ]);
            $user->save();
            dispatch(new Register($user->email, $user->activation_token));
            return BaseResponse::customResponse(
                'Successfully created user! Please check mail for active your account',
                [],
                true,
                200,
                201,
                'Created'
            );
        }

    }

    /**
     * Login user and create token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse [string] access_token
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        $credentials = request(['email', 'password']);
        $credentials['active'] = Config('apistatus.user.active');
        $credentials['deleted_at'] = null;
        if (!Auth::attempt($credentials))
            return BaseResponse::customResponse(
                'Username or password is incorrect',
                [],
                false,
                Config('error_constant.auth.username_or_password_incorrect'),
                401,
                'Unauthorized'
            );
        $user = $request->user();
        $res = $this->requestOauthToken([
            'grant_type' => env('GRANT_TYPE'),
            'client_id' => env('CLIENT_ID'),
            'client_secret' => env('CLIENT_PASSWORD'),
            'username' => $request->email,
            'password' => $request->password,
            'scope' => '',
        ]);

        $rewardRule = new RewardRules($user);
        $rewardRule->whenLogin();
        $user->avatar = media_url_web($user->avatar);

        // COPY FROM ADMIN LOGIN
        // $tokenResult = $user->createToken('Personal Access Token');
        // $token = $tokenResult->token;
        // if ($request->remember_me)
        //     $token->expires_at = Carbon::now()->addWeeks(1);
        // $token->save();

        return BaseResponse::customResponse(
            'Sign in successfully',
            [
                'access_token' => $res["access_token"],
                'refresh_token' => $res["refresh_token"],
                // 'access_token' => $tokenResult->accessToken,
                'profile' => $user,
                'token_type' => 'Bearer'
            ],
            true,
            200,
            200,
            'Success'
        );


    }

    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean',
            'token_device' => 'string',
            'device_type' => 'string'
        ]);
        $credentials = request(['email', 'password']);
        $credentials['active'] = Config('apistatus.user.active');
        $credentials['deleted_at'] = null;
        if (!Auth::attempt($credentials))
            return BaseResponse::customResponse(
                'Username or password is incorrect',
                [],
                false,
                Config('error_constant.auth.username_or_password_incorrect'),
                401,
                'Unauthorized'
            );
        $user = $request->user();
        $is_exist = true;
        if ($user->type === User::LEADER) {
            $company = Company::getCompanyByUser($user->id);
            $is_exist = $company ? true : false;
        }
        if (in_array($user->type, [User::ADMIN, User::LEADER, User::EDITOR, User::TEACHER]) && $is_exist) {
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            if ($request->remember_me)
                $token->expires_at = Carbon::now()->addWeeks(1);
            $token->save();
            $user->avatar = media_url_web($user->avatar);
            return BaseResponse::customResponse(
                'Sign in successfully',
                [
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
                    'profile' => $user
                ],
                true,
                200,
                200,
                'Success'
            );
        } else {
            return BaseResponse::customResponse(
                'Permission denied',
                [],
                false,
                403,
                403,
                "Permission denied",
                []
            );
        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        try {
            $res = $this->requestOauthToken([
                'grant_type' => 'refresh_token',
                'refresh_token' => $request->input('refresh_token'),
                'client_id' => env('CLIENT_ID'),
                'client_secret' => env('CLIENT_PASSWORD'),
                'scope' => '',
            ], $statusCode);

            if ($statusCode !== 200) {
                throw new \Exception($res['error_description'] ?? $res['message'] ?? 'invalid_grant');
            }

            return BaseResponse::customResponse(
                'Refresh successfully',
                [
                    'access_token' => $res["access_token"],
                    'refresh_token' => $res["refresh_token"],
                    'token_type' => $res["token_type"]
                ],
                true,
                200,
                200,
                'Success'
            );
        } catch (\Exception $e) {
            return BaseResponse::customResponse(
                'Refresh token expired' . $e->getMessage(),
                [],
                false,
                401,
                401,
                'Unauthorized'
            );
        }

    }


    /**
     * Logout user (Revoke the token)
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return BaseResponse::customResponse(
            'Successfully logged out',
            [],
            true,
            200,
            200,
            'Success'
        );
    }

    /**
     * Get the authenticated User
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse [json] user object
     */
    public function user(Request $request)
    {
        $user = $request->user();
        $user->avatar = filter_var($user->avatar, FILTER_VALIDATE_URL) ? $user->avatar : media_url_web($user->avatar);
        $user->level = ['translations' => $user->levelUser->getTranslationsArray()];
        if ($user->type === User::LEADER) {
            $company = Company::getCompanyByUser($user->id);
            $user->is_enterprise = $company ? true : false;
            $user->url = $company ? env('CMS_LOGIN_LINK') : '';
        }
        if ($user->type === User::TEACHER) {
            $classes = UserClass::query()->where('user_id', $user->id)->get();
            $user->has_class = $classes ? true : false;
            $user->url = $classes ? env('CMS_LOGIN_LINK') : '';
        }
        $rewardRule = new RewardRules($user);
        $rewardRule->whenLogin();
        return BaseResponse::customResponse(
            'Successfully get user profile',
            $request->user(),
            true,
            200,
            200,
            'Success'
        );
    }

    /**
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    public function signupActivate(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string',
            'password' => ['required',
                'regex:/(^(?=.*[a-zA-Z])(?=.*[0-9])(?=.{8,16}))/'
            ]
        ]);
        $user = User::where('activation_token', $request->activation_token)->where('email', $request->email)->first();
        if (!$user) {
            return BaseResponse::customResponse(
                'This activation code is invalid.',
                [],
                false,
                Config('error_constant.auth.user_not_found'),
                404,
                'NotFound'
            );
        }
        if (!$user->active) {
            $user->active = true;
            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->avatar = RandomAvatar::random();
            $user->level = InitLevel::init();
            $user->password = bcrypt($request->password);
            $user->activation_token = '';
            $user->type = User::USER;
            $user->email_verified_at = Carbon::now();
            $user->save();
            $tokenResult = $user->createToken('Personal Access Token');
            $token = $tokenResult->token;
            $token->save();
            // Add reward for user
            $rewardRule = new RewardRules($user, config('reward_constant.sign_up'));
            $rewardRule->add();

            if ($request->get('referrer_email')) {
                // Add reward for user
                $referrer_user = User::query()->where('email', $request->get('referrer_email'))->first();
                if ($referrer_user) {
                    $rewardRule = new RewardRules($referrer_user, config('reward_constant.referrer_email'));
                    $rewardRule->add();
                }

            }
            $user->avatar = media_url_web($user->avatar);

            return BaseResponse::customResponse(
                'Activation successfully',
                [
                    'access_token' => $tokenResult->accessToken,
                    'token_type' => 'Bearer',
//                    'expires_at' => Carbon::parse(
//                        $tokenResult->token->expires_at
//                    )->toDateTimeString(),
                    'profile' => $user
                ],
                true,
                200,
                200,
                'Success'
            );
        } else {
            return response()->json([
                'message' => 'this profile has been active.',
                'profile' => $user
            ]);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmailExists(Request $request)
    {
        $check = User::emailexists($request->email);
        return response()->json([
            "exists" => $check
        ]);
    }

    /**
     * @param UserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(UserRequest $request)
    {
        $user = Auth::user();
        $data_change = $request->except('_method');
        // Check update avatar
        if ($request->hasFile('avatar')) {
            $old_avatar = $user->avatar;
            $avatar = UploadService::handleUploadFile($request->file('avatar'), Config('uploadpath.avatar_folder'));
            $data_change['avatar'] = $avatar;
            // remove old avatar
            if ($old_avatar != null && !strpos($old_avatar, 'users/default-avatars') === 0) {
                UploadService::handleRemoveFile($old_avatar);
            }
        }
        if ($request->has('avatar_default')) {
            $old_avatar = $user->avatar;
            $data_change['avatar'] = explode(media_base_url(), $request->get('avatar_default'))[1];
            // remove old avatar
            if ($old_avatar != null && strpos($old_avatar, 'users/default-avatars') === false) {
                UploadService::handleRemoveFile($old_avatar);
            }
        }
        if ($request->has('password')) {
            $data_change['password'] = bcrypt($data_change['password']);
        }
        $user->update($data_change);
        $user->avatar = media_url_web($user->avatar);
        $user->level = ['translations' => $user->levelUser->getTranslationsArray()];
        return BaseResponse::customResponse(
            'Update profile successfully',
            $user,
            true,
            200,
            200,
            'Success'
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function verificationEmail(Request $request)
    {
        $token = $request->route('token');
        $user = User::query()->where('activation_token', $token)->first();
        $status = false;
        $message = 'Thông tin không hợp lệ';
        $return_link = env('WEB_LOGIN_LINK');
        if ($user && !$user->active) {
            $status = true;
            $message = 'Kích hoạt thành công !';
            if (in_array($user->type, [User::ADMIN, User::EDITOR, User::LEADER])) {
                $return_link = env('CMS_LOGIN_LINK');
            }
            $user->update(['active' => true, 'activation_token' => time(), 'email_verified_at' => Carbon::now()]);
        }
        return view('users.active', compact('status', 'message', 'return_link'));
    }

    /**
     * Exchange OAuth grant params for a token by dispatching to this app's
     * own /oauth/token route in-process, instead of a real outbound HTTP
     * call to itself. Avoids depending on an external network round-trip
     * for something the app can resolve internally, and sidesteps
     * single-threaded dev servers deadlocking on a request that calls back
     * into itself.
     *
     * @param array $params
     * @param int|null $statusCode filled with the response's HTTP status code
     * @return array decoded JSON body
     */
    private function requestOauthToken(array $params, &$statusCode = null)
    {
        $tokenRequest = \Illuminate\Http\Request::create('/oauth/token', 'POST', $params);
        $response = app()->handle($tokenRequest);
        $statusCode = $response->getStatusCode();

        return json_decode($response->getContent(), true);
    }
}
