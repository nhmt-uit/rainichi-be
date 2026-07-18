<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionTranslations extends Model
{
    public $table = 'question_translations';

    public $timestamps = false;

    protected $fillable = ['name','description'];
}
