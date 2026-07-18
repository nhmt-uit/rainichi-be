<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestFailResult extends Model
{
    public $table = 'test_fail_result';

    protected $fillable = [
        'test_fail_id',
        'test_result_id',
        'chapter_id',
        'score',
        'question',
        'total_score',
        'total_question',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function testFail()
    {
        return $this->belongsTo(TestFail::class, 'test_fail_id', 'id');
    }

}
