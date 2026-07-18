<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingTranslations extends Model
{
    public $table = 'reading_translations';

    public $fillable = ['name', 'description'];

    public $timestamps = false;
}
