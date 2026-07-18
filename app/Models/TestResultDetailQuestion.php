<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResultDetailQuestion extends Model
{
    public $table = 'test_result_detail_question';

    public $timestamps = true;
    public $fillable = ['result_detail_id', 'question_id', 'status'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function testResultDetail()
    {
        return $this->belongsTo(TestResultDetail::class, 'id', 'test_result_detail_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id', 'id');
    }
}
