<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTokenDevice extends Model
{
    public $timestamps = true;

    protected $table = 'user_token_device';

    protected $fillable = ['user_id', 'token_device', 'type', 'status'];

    protected $casts = ['status' => 'boolean'];

    const DEVICE_TYPE = ['IOS' => 1, 'ANDROID' => 2, 'OTHER' => 3];

}
