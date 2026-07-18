<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 03/12/2018
 * Time: 10:28
 */

namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Jobs\FCMJob;
use App\Models\Question;
use App\Models\QuestionGroup;
use App\Models\Test;
use App\Models\TestChapter;
use App\Models\TestFail;
use App\Models\TestQuestions;
use App\Models\TestResult;
use App\Models\TestResultDetail;
use App\Models\TestResultDetailQuestion;
use App\Models\TestTimes;
use App\Models\TestFailResult;
use App\Service\BaseResponse;
use App\Service\PushNotification;
use App\Service\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Transformers\TestResultTransformer;

class DemoController extends Controller
{

    /**
     * Add new test.
     * @return JsonResponse
     */
    public function create() {
        try {
            dispatch(new FCMJob([], 'Rainichi', "Push demo", true));
            return BaseResponse::customResponse(
                'Successfully',
                [],
                true,
                200,
                200,
                'Success'
            );
        } catch (\Exception $e) {
            return BaseResponse::customResponse(
                'Failed'. $e->getMessage(),
                [],
                false,
                500,
                500,
                'Error'
            );
        }

    }
}
