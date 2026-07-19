<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursePriceType extends Model
{
    use \Astrotomic\Translatable\Translatable;

    protected $table = 'course_price_type';

    public $translatedAttributes = ['name'];

    public function coursePrice(){
        return $this->hasMany('App\Models\CoursePrice');
    }
}
