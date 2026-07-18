<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryTranslations extends Model
{
    protected $table = 'country_translations';
    protected $fillable = ['country_id', 'locale', 'name', 'created_at', 'updated_at'];
}
