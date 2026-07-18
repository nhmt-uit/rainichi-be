<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Foundation extends Model
{
    const TRUONG_AM = 1;
    const AM_NGAT = 2;

    use \Dimsav\Translatable\Translatable;

    public $timestamps = true;

    protected $table = 'foundation_definition';

    public $translatedAttributes = ['hiragana_definition', 'hiragana_write', 'hiragana_read', 'katakana_definition', 'katakana_write', 'katakana_read','created_by'];

    public function scopeGetByType($q, $type)
    {
        if (isset($type)) {
            $type = (int)$type;
            return $q->where('type', $type);
        }
    }
}
