<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;


use App\Models\Chapter;
use App\Models\CourseLesson;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class LessonTransformer extends TransformerAbstract
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var ChapterTransformer
     */
    private $chapterTransformer;

    protected $bought;

    function __construct(Manager $fractal, ChapterTransformer $chapterTransformer)
    {
        $this->fractal = $fractal;
        $this->chapterTransformer = $chapterTransformer;
    }

    public function transform(CourseLesson $lesson)
    {
        $l = $lesson->lessons;
        if ($l && $l->submit) {
            $submit = $l->submit->where('user_id', Auth::user()->id)
                                ->where('course_id', $lesson->course_id)->sortByDesc('score')->first();
            return [
                'id' => $l->id,
                'course_lesson_id' => $lesson->id,
                'has_trial' => $lesson->has_trial,
                'is_active' => $lesson->is_active,
                'type' => $l->type,
                'sort_order' => $lesson->sort_order,
                'image' => media_url_web( $l->avatar),
                'translations' => $l->getTranslationsArray(),
                'chapters' => $this->fractal->createData(new Collection($l->chapters, $this->chapterTransformer))->toArray()['data'],
                'status' => $submit ? $submit->status : 0,
                'stars' => self::getStar($submit ? $submit->score : 0, $submit ? $submit->total_question : 0, $l->chapters),
                'following' => $lesson->following_lesson,
                'bought' => $this->bought,
                'course_id' => $lesson->course_id
            ];
        }
    }

    /**
     * @param $score
     * @param $total_question
     * @param $chapter
     * @return float|int
     */
    private static function getStar($score, $total_question, $chapter)
    {
        //Check lesson exists chapter exercise in list chapters
        if(array_search(Chapter::EXERCISE, array_column($chapter->toArray(), 'type'))){
            $star = 0;
            if ($total_question != 0) {
                $star = ceil(($score / $total_question) * 5);
            }
            return $star;
        } else {
            return 99;
        }
    }

    /**
     * @param $bought
     */
    public function setBought($bought)
    {
        $this->bought = $bought;
    }
}
