<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\UserTokenDevice;
use League\Fractal\TransformerAbstract;

class UserTokenDeviceTransformer extends TransformerAbstract
{

    public function transform(UserTokenDevice $data)
    {
        return [
            'id' => $data->id,
            'user_id' => $data->user_id,
            'token_device' => $data->token_device,
            'status' => $data->status,
            'type' => $data->type > 0 ? self::getDeviceName($data->type) : null
        ];
    }

    static function getDeviceName($type) {
        $device = array_keys(UserTokenDevice::DEVICE_TYPE, $type);
        return $device[0];
    }
}
