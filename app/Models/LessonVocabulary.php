<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonVocabulary extends Model
{
    public $table = 'lesson_vocabulary';

    public $fillable = ['lesson_id', 'group_chapter_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function groupChapter()
    {
        return $this->belongsTo('App\Models\GroupChapter');
    }
}
