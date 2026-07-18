<?php


namespace App\Transformers;


use App\Models\TestFailResult;
use League\Fractal\TransformerAbstract;

class TestFailResultTransformer extends TransformerAbstract
{
    public function transform(TestFailResult $testFail)
    {
        return [
            'id' => $testFail->id,
            'test_result_id' => $testFail->test_result_id,
            'chapter_id' => $testFail->chapter_id,
            'score' => $testFail->score,
            'question' => $testFail->question,
            'total_score' => $testFail->total_score,
            'total_question' => $testFail->total_question,
            'fail_score'=>$testFail->testFail->fail_score,
            'status' => (double)$testFail->score > (double)$testFail->testFail->fail_score ? true : false
        ];
    }
}
