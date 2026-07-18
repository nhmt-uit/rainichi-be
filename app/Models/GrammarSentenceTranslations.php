<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrammarSentenceTranslations extends Model
{
    public $timestamps = false;

    public $fillable = ['name', 'content', 'example'];
}
