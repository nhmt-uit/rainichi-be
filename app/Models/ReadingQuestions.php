<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingQuestions extends Model
{
    public $table = 'reading_questions';

    protected $fillable = ['question_id', 'reading_id', 'is_active'];

    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reading()
    {
        return $this->belongsTo('App\Models\Reading', 'reading_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question()
    {
        return $this->belongsTo('App\Models\Question', 'question_id', 'id');
    }
}
