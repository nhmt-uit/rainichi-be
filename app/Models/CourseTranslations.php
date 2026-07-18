<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseTranslations extends Model
{
    public $timestamps = false;

    protected $fillable = ['name', 'slug', 'description', 'landing_description', 'landing_gift'];
}
