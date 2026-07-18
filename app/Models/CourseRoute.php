<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseRoute extends Model
{
    use \Dimsav\Translatable\Translatable;

    protected $table = 'course_route';

    public $translatedAttributes = ['image', 'src'];

    public $fillable = [
      'durations',
      'course_id'
    ];

    public function course(){
        return $this->belongsTo('App\Models\Course');
    }
}
