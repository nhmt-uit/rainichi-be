<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestQuestions extends Model
{
    public $table = 'test_questions';

    protected $fillable = ['group_chapter_id', 'test_time_id', 'is_active', 'test_chapter_id'];

    public $timestamps = true;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function test()
    {
        return $this->belongsTo('App\Models\TestTimes', 'test_time_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function groupChapter()
    {
        return $this->belongsTo('App\Models\GroupChapter', 'group_chapter_id', 'id');
    }
}
