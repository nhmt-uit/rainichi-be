<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;


use App\Models\Course;
use App\Models\CourseClass;
use App\Models\ExerciseSubmit;
use App\Models\OrderByCash;
use App\Models\PurchasedCourse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class MyCourseTransformer extends TransformerAbstract
{
    const DONE = 'DONE';
    const IN_PROGRESS = 'IN_PROGRESS';
    const NEW = 'NEW';
    protected $user_id;

    public function transform(Course $course)
    {
        $user_id = $this->user_id ?? Auth::user()->id;
        $duration = null;
        $is_enterprise = false;
        $end_date = null;
        $purchase_cash = OrderByCash::query()
            ->where('course_id', $course->id)
            ->where('user_id', Auth::user()->id)->first();
        if ($purchase_cash) {
            $duration = 999999;
        } else {
            $purchase = PurchasedCourse::query()
                ->where('course_id', $course->id)
                ->where('user_id', $user_id)
                ->where(function ($q) {
                    $q->whereDate('end_date', '>=', Carbon::now())->orWhere('end_date', null);
                })
                ->orderByDesc('id')
                ->first();
            if ($purchase) {
                $duration = $purchase->end_date ? (Carbon::parse($purchase->end_date)->diffInDays(Carbon::now())) : 999999;
                $end_date = $purchase->end_date ?? null;
            } else {
                $courseValid = CourseClass::query()->availableCourse($user_id, $course->id)->first();
                $duration = $courseValid && $courseValid->expired_date ? (day_left($courseValid->expired_date)) : 999999;
                $is_enterprise = $courseValid ? true : false;
                $end_date = $courseValid && $courseValid->expired_date ? $courseValid->expired_date : null;
            }
        }

        $result = self::calculateResult($course->submit->where('user_id', $user_id)
            ->whereIn('status', [ExerciseSubmit::DONE])
            ->unique('lesson_id')->count(), count($course->lessons));
        return [
            'id' => $course->id,
            'progress' => $result['progress'],
            'status' => $result['status'],
            'result' => $result['result'],
            'has_course_children' => $course->courseType ? $course->courseType->has_course_children : false,
            'translations' => $course->getTranslationsArray(),
            'duration' => $duration,
            'is_enterprise' => $is_enterprise,
            'end_date' => $end_date ? Carbon::parse($end_date)->format('d-m-Y') : null,
        ];
    }

    private function calculateResult($submit, $lesson)
    {
        if ($lesson == 0) {
            $result = [
                'progress' => 0,
                'status' => self::NEW,
                'result' => self::NEW
            ];
        } elseif ($submit / $lesson > 1) {
            $result = [
                'progress' => 100,
                'status' => self::DONE,
                'result' => self::DONE
            ];
        } else {
            $progress = ($submit / $lesson) * 100;
            $result = [
                'progress' => round($progress),
                'status' => $progress == 100 ? self::DONE : ($progress == 0 ? self::NEW : self::IN_PROGRESS),
                'result' => $progress == 100 ? self::DONE : ($progress == 0 ? self::NEW : self::IN_PROGRESS)
            ];
        }
        return $result;
    }

    public function setUserIdParams($user_id)
    {
        $this->user_id = $user_id;
    }
}
