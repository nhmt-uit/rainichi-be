<?php

namespace App\Service;

use App\Models\Classroom;
use App\Models\User;
use App\Models\UserClass;
use Illuminate\Support\Facades\Log;

class UserClassService
{
    /**
     * @param $user_ids
     * @param $class_id
     * @return bool
     */
    public static function isAllowAddUser($num_users_add, $class_id)
    {
        $classroom = Classroom::query()->find((int)$class_id);
        $num_of_employee = $classroom ? $classroom->num_of_employee : 0;
        $num_of_current = UserClass::query()->with('user')
            ->join('users', 'users.id', '=','user_class.user_id')
            ->whereIn('users.type', [User::ENTERPRISE, User::USER])
            ->where('user_class.classroom_id', $class_id)
            ->where('user_class.is_active', true)
            ->select('user_class.*')
            ->count();
        Log::debug('Class ID: '.$classroom->name.'Number Class: '.$num_of_employee.' and current: '.$num_of_current);
        return (int)($num_users_add) + (int)($num_of_current) > (int)$num_of_employee ? false : true;
    }


}
