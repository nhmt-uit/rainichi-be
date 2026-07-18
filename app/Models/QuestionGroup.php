<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionGroup extends Model
{
    public $table = 'question_group';

    protected $fillable = ['group_chapter_id', 'question_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question()
    {
        return $this->belongsTo('App\Models\Question');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function groupChapter()
    {
        return $this->belongsTo('App\Models\GroupChapter');
    }

    /**
     * @param $q
     * @param array $group_ids
     * @return total_question and score of array $group_ids
     */
    public function scopeGetInfo($q, $group_ids){
        $question_ids = $q->whereIn('group_chapter_id', $group_ids)->pluck('question_id');
        $questionsData = Question::query()->with(['child_questions'])->whereIn('id', $question_ids)->get();
        $totalQuestion = array_sum(array_column($questionsData->toArray(), 'total_question'));
        $totalScore = array_sum(array_column($questionsData->toArray(), 'total_score'));
        return ['total_question' => $totalQuestion, 'total_score' => round($totalScore)];

    }
}
