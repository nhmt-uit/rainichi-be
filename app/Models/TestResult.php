<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestResult extends Model
{
    public $table = 'test_result';

    public $timestamps = true;

    public $fillable = ['test_id', 'user_id', 'score', 'status', 'total_question', 'total_score',
        'total_time'];

    protected $casts = [
        'status' => 'boolean'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function testResultDetail()
    {
        return $this->hasMany(TestResultDetail::class, 'test_result_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function getTotalScore()
    {
        $totalScore = 0;
        $details = $this->testResultDetail()->get();
        foreach ($details as $key => $value) {
            $totalScore += $value['score'];
        }
        return $totalScore;
    }

}
