<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\Answer;
use App\Models\Chapter;
use App\Models\Test;
use App\Models\TestFailResult;
use App\Models\TestResultDetail;
use App\Service\TestService;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class MyTestDetailTransformer extends TransformerAbstract
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var TestResultTransformer
     */

    private $detailsQuestionTransformer;

    public function __construct(Manager $fractal, TestResultDetailsQuestionTransformer $detailsQuestionTransformer)
    {
        $this->fractal = $fractal;
        $this->detailsQuestionTransformer = $detailsQuestionTransformer;
    }

    public function transform(TestResultDetail $test)
    {
        $chapter = $test->testTime && $test->testTime->chapter_id ? explode(',', $test->testTime->chapter_id) : null;
        $details = self::getFailScore($chapter, $test);
        return [
            'score' => round($test->score),
            'question' => [],
            'total_score' => round($test->total_score),
            'total_question' => $test->total_question,
            'result' => $details['status'] ? 'PASSED' : 'FAILED',
            'translations' => [
                'vi' => $chapter ? ['name' => TestService::generateNameOrImage($chapter, 'vi', 'NAME')] : null,
                'en' => $chapter ? ['name' => TestService::generateNameOrImage($chapter, 'en', 'NAME')] : null
            ],
            'details' => $details,
            'question_list' => self::getHistoryQuestions($test)
        ];
    }

    /**
     * @param $chapter_ids
     * @param $test
     * @return array
     */
    public function getFailScore($chapter_ids, $test)
    {
        $chapterData = [];
        $chapterData['status'] = true;
        foreach ($chapter_ids as $id) {
            $testFailResult = TestFailResult::query()
                ->where('chapter_id', 'like', '%' . str_replace(' ', '', $id) . '%')
                ->where('test_result_id', $test->test_result_id)->first();
            if (isset($testFailResult)) {
                $chapter_ids = explode(',', $testFailResult->chapter_id);
                $chapterData[$testFailResult->id] = (new TestFailResultTransformer)->transform($testFailResult);
                $chapterData[$testFailResult->id]['translations'] = [
                    'vi' => $chapter_ids ? ['name' => TestService::generateNameOrImage($chapter_ids, 'vi', 'NAME')] : null,
                    'en' => $chapter_ids ? ['name' => TestService::generateNameOrImage($chapter_ids, 'en', 'NAME')] : null
                ];
                if (!$chapterData[$testFailResult->id]['status'])
                    $chapterData['status'] = false;
            }
        }
        return $chapterData;
    }

    /**
     * @param $testResult
     * @return array
     */
    protected function getHistoryQuestions($testResult)
    {
        $data = [];
        $historyAnswers = json_decode($testResult->answers, true);
        foreach ($testResult->questions as $key => $result) {
            $question_id = $result->question_id;
            $question = $result->question;
            $chosen = [];
            if (isset($historyAnswers[$question_id])) {
                $chosen = $historyAnswers[$question_id];
            }
            $questionData = [
                'name' => $question->question,
                'status' => $result->status ? true : false,
                'answers' => $question->answer,
                'chosen' => $chosen
            ];
            array_push($data, $questionData);
        }
        return $data;

    }

}
