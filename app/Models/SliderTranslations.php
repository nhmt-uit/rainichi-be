<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SliderTranslations extends Model
{
    public $timestamps = false;
    protected $table = 'sliders_translations';
    protected $fillable = ['title', 'sub_title', 'content'];
}
