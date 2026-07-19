<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Base
{
    use \Astrotomic\Translatable\Translatable;
    use SoftDeletes;

    protected $table = 'promotions';

    protected $fillable = ['course_id', 'test_id', 'is_active', 'expired_at', 'created_by', 'updated_by'];

//    mapping translate
    public $translatedAttributes = ['name', 'description'];


}
