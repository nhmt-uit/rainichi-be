<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    public $table = 'answer';
    public $timestamps = false;
    protected $fillable = [
        'answer',
        'question_id',
        'is_correct',
        'is_active',
        'sort_order'
    ];

    public $casts = [
        'is_correct' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question()
    {
        return $this->belongsTo('App\Models\Question', 'question_id', 'id');
    }
}
