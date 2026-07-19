<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Country extends Base
{
    use \Astrotomic\Translatable\Translatable;
    protected $table = 'country';

    protected $fillable = [
        'id',
        'code',
        'is_active',
        'created_at',
        'updated_at',
    ];
    //    mapping translate
    public $translatedAttributes = ['name'];
    protected $casts = [
        'is_active' => 'boolean',
    ];

}
