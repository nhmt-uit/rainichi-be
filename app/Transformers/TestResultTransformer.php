<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;


use App\Models\Chapter;
use App\Models\Course;
use App\Models\Order;
use App\Models\Test;
use App\Models\TestFailResult;
use App\Models\TestResult;
use App\Models\TestTimes;
use App\Service\TestService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\Fractal\TransformerAbstract;
use PhpParser\Node\Expr\Cast\Object_;

class TestResultTransformer extends TransformerAbstract
{

    public function transform(TestResult $testResult)
    {
        return [
            'id' => $testResult->id,
            'test_id' => $testResult->test_id,
            'pass_score' => $testResult->test ? $testResult->test->minimum_score : 0,
            'score' => round($testResult->score),
            'total_question' => $testResult->total_question,
            'total_score' => round($testResult->total_score),
            'result' => $testResult->status == 0 ? "FAILED" : "PASSED",
            'date' => Carbon::parse($testResult->created_at)->format('d-m-Y'),
            'is_completed' => self::checkIsCompleted($testResult),
            'sections' => self::generateTestFail($testResult->id, $testResult->testResultDetail),
        ];
    }

    /**
     * Check test is completed or not
     * @param $testResult_
     * @return  true/false
     */
    public function checkIsCompleted($testResult)
    {
        $numResult = $testResult->testResultDetail->count();
        $numTestTime = $testResult->test->testTimes->count();
        return $numTestTime <= $numResult ? true : false;
    }

    /**
     * Generate result by each section
     * @param $test_result_id
     * @param $testResultDetails
     * @return array details each chapter
     */
    public function generateTestFail($test_result_id, $sections)
    {
        $returnData = array();
        foreach ($sections as $result) {
            $testTime = TestTimes::query()->where('id', $result->test_time_id)->first();
            $chapterData = [];
            if (isset($testTime)) {
                $chapter_ids = explode(",", $testTime->chapter_id);
                foreach ($chapter_ids as $id) {
                    $testFailResult = TestFailResult::query()
                        ->where('chapter_id', 'like', '%' . str_replace(' ', '', $id) . '%')
                        ->where('test_result_id', $test_result_id)->first();
                    if (isset($testFailResult)) {
                        $new_chapter_ids = explode(",", $testFailResult->chapter_id);
                        $chapterData[$testFailResult->id] = (new TestFailResultTransformer)->transform($testFailResult);
                        $chapterData[$testFailResult->id]['translations'] = [
                            'vi' => $chapter_ids ? ['name' => TestService::generateNameOrImage($new_chapter_ids, 'vi', 'NAME')] : null,
                            'en' => $chapter_ids ? ['name' => TestService::generateNameOrImage($new_chapter_ids, 'en', 'NAME')] : null
                        ];
                    }
                }
                $testTime['details'] = array_values($chapterData);
                $testTime['result'] = $result;
                $testTime['translations'] = [
                    'vi' => $chapter_ids ? ['name' => TestService::generateNameOrImage($chapter_ids, 'vi', 'NAME')] : null,
                    'en' => $chapter_ids ? ['name' => TestService::generateNameOrImage($chapter_ids, 'en', 'NAME')] : null
                ];
            }
            $returnData[] = $testTime;
        }
        return $returnData;
    }


}
