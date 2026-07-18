<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonTranslations extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'slug', 'description'];
}
