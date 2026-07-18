<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlphabetTranslations extends Model
{
    protected $table = 'alphabet_translations';
    public $timestamps = false;

    protected $fillable = ['meaning'];
}
