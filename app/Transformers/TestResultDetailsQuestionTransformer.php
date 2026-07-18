<?php


namespace App\Transformers;


use App\Models\TestResultDetailQuestion;
use League\Fractal\TransformerAbstract;

class TestResultDetailsQuestionTransformer extends TransformerAbstract
{
    public function transform(TestResultDetailQuestion $testQuestion)
    {
        return [
            'question_name' => $testQuestion->question->question,
            'answers' => $testQuestion->question->question,
            'status' => $testQuestion->status ? true : false,
        ];
    }
}
