<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;


use App\Models\Course;
use App\Models\ExerciseSubmit;
use App\Models\Order;
use App\Models\UserCourse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class MyLessonTransformer extends TransformerAbstract
{
    const DONE = 'DONE';
    const IN_PROGRESS = 'IN_PROGRESS';
    const NEW = 'NEW';

    public function transform(ExerciseSubmit $exerciseSubmit)
    {
        return [
            'id' => $exerciseSubmit->id,
            'progress' => $this->setProgress($exerciseSubmit->status),
            'status' => $this->setStatus($exerciseSubmit->status),
            'result' => $exerciseSubmit->pass == 1 ? self::DONE : ($exerciseSubmit->status == 0 ? self::NEW : self::IN_PROGRESS),
            'score' => self::getScore($exerciseSubmit->score, $exerciseSubmit->total_question),
            'translations' => $exerciseSubmit->lesson->getTranslationsArray(),
        ];
    }

    /**
     * @param $status
     * @return string
     */
    private function setStatus($status)
    {
        if ($status == 0 || $status == 3) {
            $result = self::NEW;
        } elseif ($status == 1) {
            $result = self::IN_PROGRESS;
        } else {
            $result = self::DONE;
        }
        return $result;
    }

    /**
     * @param $status
     * @return int
     */
    private function setProgress($status)
    {
        if ($status == 0 || $status == 3) {
            $result = 0;
        } elseif ($status == 1) {
            $result = 50;
        } else {
            $result = 100;
        }
        return $result;
    }

    /**
     * @param $score
     * @param $total_question
     * @return float|int
     */
    private static function getScore($score, $total_question)
    {
        $star = 0;
        if ($total_question != 0) {
            $star = ceil(($score / $total_question) * 100);
        }
        return $star;
    }
}
