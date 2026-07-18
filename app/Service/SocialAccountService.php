<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 30/11/2018
 * Time: 17:00
 */

namespace App\Service;

use App\Models\Level;
use App\Models\SocialAccount;
use App\Models\User;

class SocialAccountService
{
    /**
     * @param $providerUser
     * @param $social
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|mixed|null|object
     */
    public static function createOrGetUser($providerUser, $social)
    {
        $account = SocialAccount::query()->where('provider', $social)
            ->where('provider_user_id', $providerUser->id)
            ->first();
        if ($account) {
            return $account->user;
        } else {
            $email = $providerUser->email;
            $account = new SocialAccount([
                'provider_user_id' => $providerUser->id,
                'provider' => $social
            ]);
            $user = User::query()->where('email', $email)->first();
            if (!$user) {
                $user = User::create([
                    'email' => $email,
                    'name' => $providerUser->name,
                    'password' => bcrypt(config('auth.default_social_key')),
                    'avatar' => isset($providerUser->avatar) ? $providerUser->avatar :RandomAvatar::random(),
                    'activation_token' => '123',
                    'active' => true,
                    'type' => User::USER,
                    'receive_notify' => true,
                    'level' => Level::query()->where('is_foundation', true)->first()->id,
                    'credits' => 0
                ]);
            } else {
                if (isset($providerUser->avatar) && filter_var($user->avatar, FILTER_VALIDATE_URL) && $user->avatar != $providerUser->avatar) {
                    $user->avatar = $providerUser->avatar;
                    $user->save();
                }
            }
            $account->user()->associate($user);
            $account->save();
            return $user;
        }
    }
}
