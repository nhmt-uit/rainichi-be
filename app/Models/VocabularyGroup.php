<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VocabularyGroup extends Model
{
    public $table = 'vocabulary_group';

    protected $fillable = ['group_chapter_id', 'vocabulary_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vocabulary()
    {
        return $this->belongsTo('App\Models\Vocabulary');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function groupChapter()
    {
        return $this->belongsTo('App\Model\GroupChapter');
    }
}
