<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 5/7/20
 * Time: 20:43
 */

namespace App\Http\Controllers\API\Push;


use App\Http\Controllers\Controller;
use App\Jobs\FCMJob;
use App\Service\BaseResponse;

class DemoController extends Controller
{
    const TOKEN = 'cTErfmeZV-w:APA91bF-LGu9HIb9K1zkOk0kShwD-qQAPNHACpmN2_TIl_Pqx0Q2QIswYkoahorBvDkVxAypnXPPicFtWhf572xeMpnb2eUa0R4auE5JLiLvNs2CpFJGUIDmT67xfEHfffhjtRoDJcjo';


    public function index()
    {
        dispatch(new FCMJob(
            [self::TOKEN], 'Video call mỗi tuần ', 'Tuần này cũng gọi meeting nè', 2
        ));
        return BaseResponse::customResponse(
            'Successfully',
            [],
            true,
            200,
            200,
            'Created'
        );
    }
}