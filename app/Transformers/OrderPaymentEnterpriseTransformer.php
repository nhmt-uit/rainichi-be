<?php

namespace App\Transformers;

use App\Models\CourseClass;
use App\Models\CoursePriceType;
use App\Models\OrderPayment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use League\Fractal\TransformerAbstract;

class OrderPaymentEnterpriseTransformer extends TransformerAbstract
{

    public function transform(OrderPayment $order)
    {
        $course_id = $order->paymentCourse->course_id ?? null;
        $test_id = $order->paymentCourse->test_id ?? null;
        $classroom_id = $order->paymentCourse->classroom_id ?? null;
        $courseClass = CourseClass::query()->withTrashed()
            ->where('classroom_id', $classroom_id)
            ->where('order_payment_id', $order->id)
            ->where(function ($q) use ($course_id, $test_id) {
                if ($course_id) {
                    $q->where('course_id', '=', $course_id);
                } else {
                    $q->where('test_id', '=', $test_id);
                }
            })->first();
        $duration = null;
        if ($courseClass) {
            $duration = CoursePriceType::query()->where('duration', $courseClass->duration === 999999 ? 0 : $courseClass->duration)->first();
        }
        return [
            'id' => $order->id,
            'payment_method' => $order->payment_method,
            'amount' => $order->final_amount,
            'credit' => $order->credit,
            'user' => $order->user,
            'payment_provider' => $order->payment_provider,
            'payment_transaction_id' => $order->payment_transaction_id,
            'payment_status' => $order->payment_status,
            'payment_msg' => $order->payment_msg,
            'discount_amount' => $order->discount_amount,
            'discount_code' => $order->discount_code,
            'is_enterprise' => $order->is_enterprise,
            'created_at' => Carbon::parse($order->created_at)->format('d-m-Y h:m'),
            'classroom' => $courseClass && $courseClass->classroom ? (new ClassroomAdminTransformer)->transform($courseClass->classroom) : null,
            'course' => $courseClass ? $courseClass->course ?? $courseClass->test : null,
            'duration_id' => $courseClass ? $courseClass->duration : null,
            'duration' => $duration ? (new CoursePriceTypeTransformer)->transform($duration) : null
        ];
    }
}
