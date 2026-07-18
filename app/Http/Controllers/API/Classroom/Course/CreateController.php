<?php

namespace App\Http\Controllers\API\Classroom\Course;

use App\Models\CourseClass;
use App\Models\Order;
use App\Service\BaseResponse;
use App\Transformers\OrderTransformer;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreateController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addCourseClass(Request $request)
    {
        $dataCourse = $request->all();
        $courseClass = null;
        $category = $request->segment(4);
        $message = 'Creating course to class is successfully';
        try {
            // In this case, We need to handle add user to the course that the class it's owner
            $dataCourse['classroom_id'] = $request->route('id');
            $dataCourse['created_by'] = Auth::user()->id;
            $dataCourse['type'] = CourseClass::ADD_BY_ADMIN;
            $courseClass = CourseClass::firstOrCreate($dataCourse);
            $oderData = [
                'user_id' => Auth::user()->id,
                'buying_credits' => $dataCourse['buying_credits'],
                'reward_credits' => $dataCourse['reward_credits'],
                'classroom_id' => $request->route('id'),
                'customer_type_id' => Order::CUSTOMER_ENTERPRISE,
                'payment_method' => Order::OFFLINE,
                // TODO - Just hard code for testing
                'course_price_currency_id' => Order::CURRENCY_VND
            ];
            if ($category === 'courses') {
                $oderData['course_id'] = $dataCourse['course_id'];
            } else {
                $oderData['test_id'] = $dataCourse['test_id'];
            }
            $order = Order::firstOrCreate($oderData);
        } catch (QueryException  $e) {
            $message = $e->getMessage();
            Log::error('Something went wrong: ' . $e->getMessage());
        }
        if ($courseClass) {
            return BaseResponse::customResponse(
                $message,
                (new OrderTransformer)->transform($order),
                true,
                201,
                201,
                'Created'
            );
        } else {
            return BaseResponse::customResponse(
                $message,
                '',
                true,
                201,
                201,
                'Created'
            );
        }


    }
}
