<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResultDetail extends Model
{
    public $table = 'test_result_detail';

    public $timestamps = false;

    public $fillable = ['test_result_id', 'test_time_id', 'score', 'total_question', 'total_score', 'question', 'answers'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function testResult()
    {
        return $this->belongsTo(TestResult::class, 'test_result_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function testTime()
    {
        return $this->belongsTo(TestTimes::class, 'test_time_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function questions(){
        return $this->hasMany(TestResultDetailQuestion::class,'result_detail_id','id');
    }
}
