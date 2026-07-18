<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConversationGroup extends Model
{
    public $table = 'conversation_group';

    protected $fillable = ['group_chapter_id', 'conversation_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function conversation()
    {
        return $this->belongsTo('App\Models\Conversation');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function groupChapter()
    {
        return $this->belongsTo('App\Model\GroupChapter');
    }
}
