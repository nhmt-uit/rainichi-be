<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 30/11/2018
 * Time: 16:27
 */

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Models\SocialAccount;
use App\Models\User;
use App\Service\BaseResponse;
use App\Service\SocialAccountService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

use App\Service\SocialToken;

class SocialAuthController extends Controller
{
    use SocialToken;
    /**
     * @param $provider
     * @return mixed
     */
    public function redirectToProvider($provider)
    {
        return ['data' => Socialite::driver($provider)->redirect()->getTargetUrl()];
    }

    /**
     * @param $provider
     * @return \Illuminate\Http\JsonResponse
     */
    public function callback($provider)
    {
        $user = SocialAccountService::createOrGetUser(Socialite::driver($provider)->user(), $provider);
        auth()->login($user);

        $tokenResult = $user->createToken('Social Access Token');
        $token = $tokenResult->token;
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function socialLoginForMobile(Request $request)
    {
        try {
            $social_user = Socialite::driver($request->route('provider'))->userFromToken($request->get('token'));
            if (isset($social_user->email)) {
                $user = SocialAccountService::createOrGetUser($social_user, $request->route('provider'));
                auth()->login($user);
                $token = $this->getBearerTokenByUser($user, env('CLIENT_ID'), false);
                $user->avatar = filter_var($user->avatar, FILTER_VALIDATE_URL) ? $user->avatar : media_url_web($user->avatar);
                return BaseResponse::customResponse(
                    'Sign in successfully',
                    [
                        'access_token' => $token["access_token"],
                        'refresh_token' => $token["refresh_token"],
                        'token_type' => $token["token_type"],
                        'profile' => $user
                    ],
                    true,
                    200,
                    200,
                    'Success'
                );
            } else {
                return BaseResponse::customResponse(
                    'Email not exists in response data',
                    [],
                    false,
                    444,
                    401,
                    'Unauthorized',
                    []
                );
            }
        } catch (\Exception $e) {
            return BaseResponse::customResponse(
                $e->getMessage(),
                [],
                false,
                401,
                401,
                'Unauthorized',
                []
            );
        }
    }

    public function demologin(Request $request) {
        $user = User::where('email', $request->email)->first();
        $token = $this->getBearerTokenByUser($user, 1, false);
        return response()->json([
           'access' =>  $token
        ]);
    }
}
