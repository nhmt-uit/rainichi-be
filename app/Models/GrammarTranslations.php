<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrammarTranslations extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'description', 'example'];
}
