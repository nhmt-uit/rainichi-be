<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\CourseLesson;
use App\Models\Lesson;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class LessonAdminTransformer extends TransformerAbstract
{
    public function transform(Lesson $lesson)
    {
        $cl = null;
        if ($lesson->course_id) {
            $cl = CourseLesson::query()
                ->where('course_id', $lesson->course_id)
                ->where('lesson_id', $lesson->id)
                ->first();
        }

        return [
            'id' => $lesson->id,
            'name' => $lesson->name,
            'level_id' => $lesson->level_id,
            'is_active' => $lesson->is_active,
            'type' => $lesson->type,
            'in_course' => $lesson->course_id ? [
                'is_active' => $cl->is_active,
                'sort_order' => $cl->sort_order,
                'has_trial' => $cl->has_trial,
                'following' => $cl->following_lesson ? array_map('intval', explode(",", $cl->following_lesson)) : null
            ] : null,
            'image' => media_url_web( $lesson->avatar),
            'translations' => $lesson->getTranslationsArray(),
            'created_at' => Carbon::parse($lesson->created_at)->format('d-m-Y'),
            'created_by' => $lesson->user ? $lesson->user->name : ''

        ];
    }
}
