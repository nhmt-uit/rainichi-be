<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerType extends Model
{
    use \Astrotomic\Translatable\Translatable;

    protected $table = 'customer_type';

    public $translatedAttributes = ['name'];
}
