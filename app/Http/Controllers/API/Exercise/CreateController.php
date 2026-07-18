<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Exercise;


use App\Http\Controllers\Controller;
use App\Http\Requests\QuestionRequest;
use App\Models\Answer;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Models\ExerciseSubmit;
use App\Models\LessonVocabulary;
use App\Models\Level;
use App\Models\PurchasedCourse;
use App\Models\Question;
use App\Models\QuestionGroup;
use App\Models\ReadingQuestions;
use App\Models\TestQuestions;
use App\Service\BaseResponse;
use App\Service\RewardRules;
use App\Service\UploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CreateController extends Controller
{

    /**
     * @param QuestionRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(QuestionRequest $request)
    {
        $question_data = $request->all();
        //check media file
        if ($request->hasFile('media')) {

            $audio = UploadService::handleUploadFile($request->file('media'), Config('uploadpath.question_media_folder'));
            $question_data['media'] = $audio;
        }
        //check image
        if ($request->hasFile('image')) {

            $image = UploadService::handleUploadFile($request->file('image'), Config('uploadpath.question_image_folder'));
            $question_data['image'] = $image;
        }
        if ($question_data['level_id'] == 0) {
            $question_data['level_id'] = null;
        }
        $question_data['created_by'] = Auth::user()->id;
        $question = Question::create($question_data);
        if ($request->has('translations')) {
            $language_keys = array_keys($question_data['translations']);
            foreach ($language_keys as $language) {
                $question->translateOrNew($language)->name = $question_data['translations'][$language]['name'];
                if (array_key_exists('description', $question_data['translations'][$language])) {
                    $question->translateOrNew($language)->description = $question_data['translations'][$language]['description'];
                }
            }
        }
        // get answer list
        if ($request->has('answers')) {
            $answers = $question_data['answers'];
            foreach ($answers as $answer) {
                $answer['question_id'] = $question->id;
                Answer::query()->create($answer);
            }
        }
        if ($question->save()) {
            if ($request->has('group_chapter_id')) {
                $question->groupQuestion()->sync([$request->get('group_chapter_id')]);
            }
            return BaseResponse::customResponse(
                'Create successfully',
                $question,
                true,
                200,
                201,
                'Created'
            );
        } else {
            return BaseResponse::customResponse(
                'Fail to insert',
                [],
                false,
                Config('error_constant.question.insert_fail'),
                422,
                'Unprocessed Entity'
            );
        }
    }

    /**
     * Add conversation to group
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToGroup(Request $request)
    {
        $conversation = $request->get('question_id');
        foreach ($conversation as $c) {
            $data = QuestionGroup::query()->where('question_id', $c)->where('group_chapter_id', $request->route('group_id'))->first();
            if (!$data) {
                QuestionGroup::query()->create([
                    'question_id' => $c,
                    'group_chapter_id' => $request->route('group_id')
                ]);
            }
        }
        return BaseResponse::customResponse(
            'Create successfully',
            [],
            true,
            200,
            201,
            'Created'
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function submitExercise(Request $request)
    {
        $user = Auth::user();
        $data = $request->all();
        $data['pass'] = false;
        $data['user_id'] = $user->id;
        $status = false;
        $has_done = false;
        $has_reward = false;
        $data_submit = ExerciseSubmit::query()
            ->where('lesson_id', $data['lesson_id'])
            ->where('course_id', $data['course_id'])
            ->where('user_id', $data['user_id'])
            ->first();
        $current_lesson = CourseLesson::query()
            ->where('lesson_id', $data['lesson_id'])
            ->where('course_id', $data['course_id'])
            ->first();
        if ($data_submit) {
            // update score when new score greater than current score.
            if ($data['score'] / $data['total_question'] >= ExerciseSubmit::PASS_SCORE) {
                $status = true;
                if ($data['score'] > $data_submit->score) {
                    $data['pass'] = true;
                    $data['status'] = ExerciseSubmit::DONE;
                    $next_lesson = CourseLesson::query()
                        ->where('following_lesson', $data['lesson_id'])
                        ->where('course_id', $data['course_id'])
                        ->first();
                    $data_submit->update($data);
                    $has_done = self::upgradeLevel($user, $data['course_id']);
                    if (!isset($next_lesson))
                        $next_lesson = CourseLesson::query()
                            ->where('course_id', $data['course_id'])
                            ->where('sort_order', $current_lesson->sort_order + 1)
                            ->first();
                    self::newSubmit($next_lesson->lesson_id, $data);
                }
            } else {
                if ($data['score'] > $data_submit->score) {
                    $data_submit->update($data);
                }
            }
        } else {
            if ($data['score'] / $data['total_question'] >= ExerciseSubmit::PASS_SCORE) {
                $data['pass'] = true;
                $data['status'] = ExerciseSubmit::DONE;
                $status = true;
                $next_lesson = CourseLesson::query()
                    ->where('following_lesson', $data['lesson_id'])
                    ->where('course_id', $data['course_id'])
                    ->first();
                ExerciseSubmit::query()->create($data);
                $has_done = self::upgradeLevel($user, $data['course_id']);
                if (!isset($next_lesson))
                    $next_lesson = CourseLesson::query()
                        ->where('course_id', $data['course_id'])
                        ->where('sort_order', $current_lesson->sort_order + 1)
                        ->first();
                self::newSubmit($next_lesson->lesson_id, $data);
            } else {
                $data['pass'] = false;
                $data['status'] = ExerciseSubmit::IN_PROGRESS;
                ExerciseSubmit::query()->create($data);
            }

        }
        if ($data['score'] == $data['total_question']) {
            $rewardRule = new RewardRules($user, config('reward_constant.five_star_course'));
            $rewardRule->add();
        }
        return BaseResponse::customResponse(
            'Submit successfully',
            ['status' => $status, 'has_done' => $has_done],
            true,
            200,
            201,
            'Created'
        );
    }

    /**
     * @param $next_lesson
     * @param $data
     */
    public function newSubmit($next_lesson, $data)
    {
        $next_lesson_array = explode(",", $next_lesson);
        foreach ($next_lesson_array as $lesson_id) {
            Log::info('This is some useful information.' . $lesson_id);
            $exercise_submit = ExerciseSubmit::query()
                ->where('lesson_id', $lesson_id)
                ->where('course_id', $data['course_id'])
                ->where('user_id', $data['user_id'])
                ->first();
            if (!$exercise_submit)
                ExerciseSubmit::query()->create([
                    'course_id' => $data['course_id'],
                    'lesson_id' => $lesson_id,
                    'total_question' => 0,
                    'user_id' => $data['user_id'],
                    'status' => ExerciseSubmit::NOT_START
                ]);
        }
    }

    /**
     * @param $user
     * @param $course_id
     * @return bool
     */
    static function upgradeLevel($user, $course_id)
    {
        $has_done = false;
        $duration = PurchasedCourse::query()
            ->where('course_id', $course_id)
            ->where('user_id', $user->id)
            ->where(function ($q) {
                $q->whereDate('end_date', '>=', Carbon::now())->orWhere('end_date', null);
            })->first();
        //only upgrade level when course bought already.
        if (!empty($duration)) {
            $course = Course::query()->with('lessons')->find($course_id);
            $course_lesson = count($course->lessons);
            $course_submit = ExerciseSubmit::query()
                ->where('course_id', $course_id)
                ->where('status', ExerciseSubmit::DONE)
                ->where('user_id', $user->id)->distinct()->select('lesson_id')->count();
            if ($course_submit == $course_lesson) {
                $current_level = Level::query()->find($user->level);
                if ($current_level && in_array($course->level_id, explode(",", $current_level->next_level))) {
                    $user->level = $course->level_id;
                    $user->save();

                }
                $has_done = true;
            }

        }
        return $has_done;
    }
}
