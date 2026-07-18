<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 03/12/2018
 * Time: 10:28
 */

namespace App\Http\Controllers\API\Test;


use App\Http\Controllers\Controller;
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
use App\Service\UploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Transformers\TestResultTransformer;

class CreateController extends Controller
{

    /**
     * Add new test.
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $test_data = $request->all();
        //check audio file
        if ($request->hasFile('video')) {

            $video = UploadService::handleUploadFile($request->file('video'), Config('uploadpath.test_folder'));
            $test_data['video'] = $video;
        }
        if ($request->hasFile('image')) {

            $image = UploadService::handleUploadFile($request->file('image'), Config('uploadpath.test_images_folder'));
            $test_data['image'] = $image;
        }
        $test_data['created_by'] = Auth::user()->id;
        if ($test_data['level_id'] == 0) {
            $test_data['level_id'] = null;
        }
        $test = Test::create($test_data);

        // get translation keys and add to translation table
        $language_keys = array_keys($test_data['translations']);
        foreach ($language_keys as $language) {
            $test->translateOrNew($language)->name = $test_data['translations'][$language]['name'];
            $test->translateOrNew($language)->description = $test_data['translations'][$language]['description'];
        }
        if ($test->save()) {
            $test_time = $test_data['test_time'];
            foreach ($test_time as $t) {
                $t['test_id'] = $test->id;
                TestTimes::query()->create($t);
                if ($t['chapter_id']) {
                    self::saveChapterInExam($t['chapter_id'], $test->id);
                }
            }
            $test_fail = $test_data['test_fail'];
            foreach ($test_fail as $tf) {
                $tf['test_id'] = $test->id;
                TestFail::query()->create($tf);
            }
//            if ($request->has('question_id')) {
//                $question_id = $request->get('question_id');
//                foreach ($question_id as $q) {
//                    TestQuestions::query()->create([
//                        'question_id' => $q,
//                        'test_id' => $test->id,
//                        'is_active' => true
//                    ]);
//                }
//            }
            return BaseResponse::customResponse(
                'Create successfully',
                $test,
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
                Config('error_constant.test.insert_fail'),
                422,
                'Unprocessable Entity'
            );
        }

    }

    /**
     * @param $list_chapter
     * @param $test_id
     */
    public function saveChapterInExam($list_chapter, $test_id)
    {
        $chapter = (explode(",", $list_chapter));

        foreach ($chapter as $c) {
            TestChapter::query()->create([
                'test_id' => $test_id,
                'chapter_id' => $c
            ]);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function addQuestionToTest(Request $request)
    {
        $question_group = $request->question_group;
        $testTime = TestTimes::query()->find($request->test_time_id);
        $test_chapter = TestChapter::query()->where('chapter_id', $request->chapter_id)
            ->where('test_id', $testTime->test_id)->first();
        foreach ($question_group as $group) {
            $check = TestQuestions::query()
                ->where('test_time_id', $request->test_time_id)
                ->where('group_chapter_id', $group)
                ->where('test_chapter_id', $test_chapter->id)
                ->first();
            if (!$check) {
                TestQuestions::query()->create([
                    'test_time_id' => $request->test_time_id,
                    'group_chapter_id' => $group,
                    'test_chapter_id' => $test_chapter->id,
                    'is_active' => true
                ]);
            }
        }
        return BaseResponse::customResponse(
            'Successfully',
            [],
            true,
            200,
            201,
            'Created'
        );

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function submitTest(Request $request)
    {

        $test_id = $request->route('test_id');
        $minimum_score = Test::query()->find($test_id)->minimum_score;
        $test_data = $request->get('test_result');
        $score = 0;
        $test_result = TestResult::query()->create([
            'test_id' => $test_id,
            'user_id' => Auth::user()->id,
            'score' => 0,
            'status' => 0
        ]);
        foreach ($test_data as $t) {
            $score += (($t['score'] / $t['total_question']) * 100);
            TestResultDetail::query()->create([
                'test_time_id' => $t['test_section_id'],
                'score' => round($t['score']),
                'total_question' => $t['total_question'],
                'test_result_id' => $test_result->id
            ]);
        }
        $status = $score >= $minimum_score ? 1 : 0;
        $test_result->score = round($score);
        $test_result->status = $status;
        $test_result->save();
        return BaseResponse::customResponse(
            'Submit success',
            $test_result,
            true,
            200,
            200,
            'Success',
            []
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function submitTestDetails(Request $request)
    {

        $test_result_id = $request->route('test_result_id');
        $test_result = TestResult::query()->find($test_result_id);

        if ($test_result) {
            $test_data = $request->get('test_result');
            $minimum_score = Test::query()->find($test_result->test_id)->minimum_score;

            $dataQuestion = [];
            $result_question = [];
            $resultTestTimeIds = [];
            $testStatus = true;

            #get question form request
            foreach ($test_data as $data) {
                $resultTestTimeIds = array_merge($resultTestTimeIds, [$data['test_section_id']]);
                $result_question = $result_question + $data['questions'];
            }

            #this code will be remove after success
            $totalScoreMain = 0;
            $totalQuestionMain = 0;
            $questionScoreMain = [];

            $testTimeChapterIds = [];
            $testTimes = TestTimes::query()->whereIn('id', $resultTestTimeIds)->select('chapter_id')->get();
            foreach ($testTimes as $ts) {
                $testTimeChapterIds = array_merge($testTimeChapterIds, explode(',', $ts->chapter_id));
            }

            $testFail = TestFail::query()->where('test_id', $test_result->test_id)->get();
            #calculator pass or fail each chapter in test fail
            foreach ($testFail as $key => $tf) {
                $chapper_ids = explode(',', $tf->chapter_id);
                $questionScore = [];
                $questionIds = [];

                $questions = Question::query()->with(['child_questions'])
                    ->join('question_group', 'question_group.question_id', '=', 'question.id')
                    ->join('test_questions', 'test_questions.group_chapter_id', '=', 'question_group.group_chapter_id')
                    ->join('test_chapter', 'test_chapter.id', '=', 'test_questions.test_chapter_id')
                    ->whereIn('test_chapter.chapter_id', $chapper_ids)
                    ->where('test_chapter.test_id', $test_result->test_id)
                    ->select('question.*')->get();

                $totalScore = array_sum(array_column($questions->toArray(), 'total_score'));

                foreach ($questions as $key => $question) {
                    $dataScore = [$question->id => $question->score];
                    $dataId = [$question->id];
                    if ($question->child_questions->count() > 0) {
                        $dataScore = $question->child_questions->pluck('score', 'id')->toArray();
                        $dataId = $question->child_questions->pluck('id')->toArray();
                    }
                    $questionScore = $questionScore + $dataScore;
                    $questionIds = array_merge($questionIds, $dataId);
                }

                $questionScoreMain = $questionScoreMain + $questionScore;
                $countCorrect = 0;
                $correctScore = 0;
                foreach ($result_question as $qId => $result) {
                    if (in_array($qId, $questionIds) && $result === true) {
                        $countCorrect++;
                        $correctScore += $questionScore[$qId];
                    }
                }

                #get total score each part
                $totalScoreMain += $totalScore;
                $totalQuestionMain += sizeof($questionIds);
                $testFailResult = TestFailResult::query()->where('test_fail_id', $tf->id)
                    ->where('test_result_id', $test_result->id)->first();
                $data_fail = [
                    'chapter_id' => $tf->chapter_id,
                    'score' => $correctScore,
                    'question' => $countCorrect,
                    'total_score' => round($totalScore),
                    'total_question' => sizeof($questionIds)
                ];

                #this code will be refactor after success
                if (isset($testFailResult)) {
                    $correctScore = $testFailResult->score;
                    $countCorrect = $testFailResult->question;
                }

                if (empty(array_diff($chapper_ids, $testTimeChapterIds))) {

                    if (isset($testFailResult)) {
                        #this code will be remove after success
                        $testFailResult->update($data_fail);
                    } else {
                        $data_fail['test_fail_id'] = $tf->id;
                        $data_fail['test_result_id'] = $test_result->id;
                        TestFailResult::query()->create($data_fail);
                    }
                }

                if ($tf->fail_score >= $correctScore)
                    $testStatus = false;

                #this code will be remove after success
                $new_items = array(
                    'user_id' => Auth::user()->id,
                    'test_result_id' => $test_result->id,
                    'test_id' => $test_result->test_id,
                    'chapter_id' => $tf->chapter_id,
                    'chapter_ids' => explode(',', $tf->chapter_id),
                    'fail_score' => $tf->fail_score,
                    'question_ids' => $questionIds,
                    'numberCorrect' => $countCorrect,
                    'correctScore' => $correctScore,
                    'totalScore' => $totalScore,
                    'status' => $tf->fail_score < $correctScore

                );
                array_push($dataQuestion, $new_items);
            }

            #this code will be remove after success
            $dataQuestion['totalScore'] = $totalScoreMain;
            $dataQuestion['totalQuestionMain'] = $totalQuestionMain;


            foreach ($test_data as $t) {
                $resultCorrect = 0;
                $resultScore = 0;
                foreach ($t['questions'] as $key => $val) {
                    if ($val === true && array_key_exists($key, $questionScoreMain)) {
                        $resultCorrect += 1;
                        $resultScore += $questionScoreMain[$key];
                    }
                }
                $questionsData = Question::getQuestionsByTestTime($t['test_section_id']);
                $totalQuestion = array_sum(array_column($questionsData->toArray(), 'total_question'));
                $totalScore = array_sum(array_column($questionsData->toArray(), 'total_score'));

                $data_detail = [
                    'score' => $resultScore,
                    'total_score' => $totalScore,
                    'total_question' => $totalQuestion,
                    'question' => $resultCorrect,
                    'answers' => isset($t['answers']) ? json_encode($t['answers']) : null
                ];

                $result_details = TestResultDetail::query()->where('test_time_id', $t['test_section_id'])
                    ->where('test_result_id', $test_result->id)->first();

                if (isset($result_details)) {
                    $result_details->update($data_detail);
                } else {
                    $data_detail['test_time_id'] = $t['test_section_id'];
                    $data_detail['test_result_id'] = $test_result->id;
                    $result_details = TestResultDetail::create($data_detail);
                }
                if ($result_details) {
                    $questions = Question::getQuestionsByTestTime($result_details->test_time_id);
                    $question_ids = [];
                    foreach ($questions as $key => $q) {
                        $dataId = [$q->id];
                        if ($q->child_questions->count() > 0) {
                            $dataId = $q->child_questions->pluck('id')->toArray();
                        }
                        $question_ids = array_merge($question_ids, $dataId);
                    }
                    #add log for debug in Prod
                    Log::debug('Questions real' . $t['test_section_id'] . ':' . json_encode($question_ids));

                    foreach ($question_ids as $key => $id) {

                        $status = array_key_exists($id, $t['questions']) && $t['questions'][$id] === true ? true : false;
                        $data = [
                            'result_detail_id' => $result_details->id,
                            'question_id' => $id,
                            'status' => $status
                        ];
                        $resultQuestion = TestResultDetailQuestion::where('result_detail_id', $result_details->id)
                            ->where('question_id', $id)->first();
                        if ($resultQuestion)
                            $resultQuestion->update(['status' => $status]);
                        else
                            TestResultDetailQuestion::create($data);
                    }
                }
            }
            #add log for debug in Prod
            Log::debug('Data exam of user ' . Auth::user()->id . ':' . json_encode($dataQuestion));

            $score = $test_result->getTotalScore();
            $status = ($score >= $minimum_score) && $testStatus ? 1 : 0;
            $test_result->score = $score;
            $test_result->status = $status;
            $test_result->save();

            return BaseResponse::customResponse(
                'Submit success',
                (new TestResultTransformer)->transform($test_result),
                true,
                200,
                200,
                'Success',
                []
            );

        } else {
            return BaseResponse::customResponse(
                'Something went wrong. Test result id not found',
                $test_result,
                true,
                500,
                500,
                'Internal Error',
                []
            );
        }

    }

    /**
     * @param $test_id
     * @return JsonResponse
     *
     */
    public function createTestResult(Request $request)
    {
        $testId = $request->route('test_id');
        $totalTime = 0;
        $totalQuestion = 0;
        $totalScore = 0;
        if ($testId) {
            $testTime = TestTimes::query()->where('test_id', $testId)->get();
            foreach ($testTime as $tt) {
                $totalTime += (int)$tt->time;
                $questionsData = Question::getQuestionsByTestTime($tt->id);
                $totalQuestion += array_sum(array_column($questionsData->toArray(), 'total_question'));
                $totalScore += array_sum(array_column($questionsData->toArray(), 'total_score'));
            }

            $test_result = TestResult::query()->create([
                'test_id' => $testId,
                'user_id' => Auth::user()->id,
                'score' => 0,
                'total_question' => (int)$totalQuestion,
                'total_score' => round($totalScore),
                'total_time' => (int)$totalTime,
                'status' => 0
            ]);
            return BaseResponse::customResponse(
                'Create test result success',
                $test_result,
                true,
                200,
                200,
                'Success',
                []
            );
        } else {
            return BaseResponse::customResponse(
                'Test id not found',
                $testId,
                true,
                200,
                200,
                'Fail',
                []
            );
        }
    }


}
