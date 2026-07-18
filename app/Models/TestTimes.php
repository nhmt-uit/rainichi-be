<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestTimes extends Model
{
    public $table = 'test_times';

    protected $fillable = [
        'test_id',
        'chapter_id',
        'time',
        'questions'
    ];
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function testQuestion()
    {
        return $this->hasMany('App\Models\TestQuestions', 'test_time_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function chapter()
    {
        return $this->belongsTo(Chapter::class, 'chapter_id', 'id');
    }
}
