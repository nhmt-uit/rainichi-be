<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Listening extends Model
{
    public $timestamps = 'listening';

    public $fillable = [
        'question_id',
        'audio',
        'sub_title'
    ];
}
