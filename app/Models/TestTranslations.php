<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestTranslations extends Model
{
    public $table = 'test_translations';
    public $timestamps = false;
    protected $fillable = ['name', 'description'];
}
