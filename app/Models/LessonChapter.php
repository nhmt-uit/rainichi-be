<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LessonChapter extends Model
{
    public $timestamps = false;

    protected $table = 'lesson_chapter';

    protected $fillable = ['chapter_id', 'lesson_id'];

}
