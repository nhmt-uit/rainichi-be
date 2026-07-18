<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KanjiTranslations extends Model
{
    public $timestamps = false;
    protected $fillable = ['chinese_vietnamese_word', 'meaning', 'example1', 'example2'];
}
