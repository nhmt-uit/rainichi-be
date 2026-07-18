<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrammarSentence extends Model
{
    use \Dimsav\Translatable\Translatable;

    public $timestamps = true;

    protected $table = 'grammar_sentence';

    public $fillable = ['grammar_id', 'is_active', 'sort_order'];

    public $translatedAttributes = ['name', 'content', 'example'];

    public $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grammar()
    {
        return $this->belongsTo('App\Models\Grammar', 'grammar_id');
    }
}
