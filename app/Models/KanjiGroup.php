<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KanjiGroup extends Model
{
    public $table = 'kanji_group';

    protected $fillable = ['group_chapter_id', 'kanji_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function kanji()
    {
        return $this->belongsTo('App\Models\Kanji');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function groupChapter()
    {
        return $this->belongsTo('App\Models\GroupChapter');
    }
}
