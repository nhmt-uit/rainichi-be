<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alphabet extends Model
{
    use \Astrotomic\Translatable\Translatable;

    const HIRAGANA = 1;
    const KATAKANA = 2;
    const KANJI = 3;

    const TYPES = array(
        'hiragana'=>self::HIRAGANA,
        'katakana'=>self::KATAKANA,
        'kanji'=>self::KANJI,
    );


    protected $table = 'alphabet';

    public $timestamps = true;

    protected $fillable = ['character', 'image', 'audio', 'type','created_by', 'updated_by'];

    public $translatedAttributes = ['meaning'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function created_by()
    {
        return $this->belongsTo('App\Models\User', 'created_by', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function updated_by()
    {
        return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    }

    public function scopeGetByType($q, $type)
    {
        if (isset($type)) {
            $type = (int)$type;
            return $q->where('type', $type);
        }
    }
}
