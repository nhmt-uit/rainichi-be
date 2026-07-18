<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Number extends Model
{
    protected $table = 'number';

    public $timestamps = true;

    public $fillable = ['number', 'instance', 'audio','created_by'];
}
