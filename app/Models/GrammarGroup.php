<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrammarGroup extends Model
{
    public $table = 'grammar_group';

    protected $fillable = ['group_chapter_id', 'grammar_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grammar()
    {
        return $this->belongsTo('App\Models\Grammar');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function groupChapter()
    {
        return $this->belongsTo('App\Model\GroupChapter');
    }
}
