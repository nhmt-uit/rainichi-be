<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestChapter extends Model
{
    public $timestamps = true;

    protected $table = 'test_chapter';

    protected $fillable = ['chapter_id', 'test_id'];
}
