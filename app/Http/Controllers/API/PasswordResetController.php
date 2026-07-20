<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Service\BaseResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordResetController extends Controller
{
    /**
     * Create token password reset
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse [string] message
     */
    public function create(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
        ]);
        $user = User::where('email', $request->email)->first();
        if (!$user)
            return response()->json([
                'message' => 'We cant find a user with that e-mail address.'
            ], 404);
        $passwordReset = PasswordReset::updateOrCreate(
            ['email' => $user->email],
            [
                'email' => $user->email,
                'token' => Str::random(60)
            ]
        );
        if ($user && $passwordReset)
        dispatch(new \App\Jobs\PasswordReset($user->email, $passwordReset->token));
        return response()->json([
            'message' => 'We have e-mailed your password reset link!'
        ]);
    }

    /**
     * Find token password reset
     *
     * @param  [string] $token
     * @return \Illuminate\Http\JsonResponse [string] message
     */
    public function find($token)
    {
        return view('reset-password', ['token' => $token]);
    }

    /**
     * Reset password
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse [string] message
     */
    public function reset(Request $request)
    {
        $passwordReset = PasswordReset::where('token', $request->get('token'))
            ->first();
        if (!$passwordReset)
            return redirect()->back()->with('error', 'Token không chính xác');
        if (Carbon::parse($passwordReset->updated_at)->addMinutes(720)->isPast()) {
            $passwordReset->delete();
            return redirect()->back()->with('error', 'Token đã hết hạn');
        }
        $request->validate([
            'password' => ['required',
                'regex:/(^(?=.*[a-zA-Z])(?=.*[0-9])(?=.{8,16}))/',
                'confirmed'
            ]
        ], [
            'password.regex' => 'Mật khẩu phải chứa ít nhất 1 số và phải có ít nhất 8 kí tự',
            'password.confirmed' => 'Mật khẩu xác nhận không trùng khớp'
        ]);
        $user = User::where('email', $passwordReset->email)->first();
        if (!$user)
            return redirect()->back()->with('error', 'Email không tồn tại');
        $user->password = bcrypt($request->get('password'));
        $user->save();
        $passwordReset->delete();
        $user->notify(new PasswordResetSuccess($passwordReset));
        return redirect()->back()->with('success', 'Cập nhật mật khẩu thành công.');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePassword(Request $request)
    {
        $user = $request->user();

        /*
        * Validate all input fields
        */
        $rules = [
            'password' => 'required',
            'new_password' => ['required|different:password',
                'regex:/(^(?=.*[a-zA-Z])(?=.*[0-9])(?=.{8,16}))/']
        ];
        $messages = [
            'new_password.different' => 'The new password and password must be different.',
            'errors' => [

            ]
        ];
        $validator = \Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return BaseResponse::customResponse(
                $validator->errors()->first(),
                [],
                false,
                19,
                422,
                'Unprocessable Entity'
            );
        }

        if (Hash::check($request->password, Auth::user()->password)) {
            $user->fill([
                'password' => bcrypt($request->new_password)
            ])->save();
            $http = new \GuzzleHttp\Client;
            $response = $http->post(env('APP_URL') . 'oauth/token', [
                'form_params' => [
                    'grant_type' => env('GRANT_TYPE'),
                    'client_id' => env('CLIENT_ID'),
                    'client_secret' => env('CLIENT_PASSWORD'),
                    'username' => $user->email,
                    'password' => $request->new_password,
                    'scope' => '',
                ],
            ]);

            $res = json_decode((string)$response->getBody(), true);
            return BaseResponse::customResponse(
                'Password changed',
                [
                    'access_token' => $res["access_token"],
                    'refresh_token' => $res["refresh_token"],
                    'token_type' => $res["token_type"],
                    'profile' => $user
                ],
                true,
                200,
                200,
                'Success'
            );

        } else {
            return BaseResponse::customResponse(
                'Password does not match',
                [],
                false,
                Config('error_constant.auth.user_not_found'),
                400,
                'Bad request'
            );
        }
    }
}
