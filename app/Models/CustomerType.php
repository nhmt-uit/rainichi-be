<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerType extends Model
{
    use \Dimsav\Translatable\Translatable;

    protected $table = 'customer_type';

    public $translatedAttributes = ['name'];
}
