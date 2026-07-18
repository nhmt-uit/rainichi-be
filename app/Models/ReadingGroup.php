<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingGroup extends Model
{
    public $table = 'reading_group';

    protected $fillable = ['reading_id', 'group_chapter_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reading()
    {
        return $this->belongsTo('App\Models\Reading');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function groupChapter()
    {
        return $this->belongsTo('App\Model\GroupChapter');
    }
}
