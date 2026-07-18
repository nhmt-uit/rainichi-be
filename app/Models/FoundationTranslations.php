<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FoundationTranslations extends Model
{
    public $timestamps = false;
    
    protected $table = 'foundation_definition_translations';

    protected $fillable = ['hiragana_definition', 'hiragana_write', 'hiragana_read', 'katakana_definition', 'katakana_write', 'katakana_read'];
}
