<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestFail extends Model
{
    public $table = 'test_fail';

    protected $fillable = [
        'test_id',
        'chapter_id',
        'fail_score',
    ];

    public $timestamps = false;
}
