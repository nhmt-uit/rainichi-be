<?php

namespace App\Models;

use Astrotomic\Translatable\Translatable;

class Configuration extends Base
{
    use Translatable;

    protected $table = 'configuration';

    protected $fillable = ['address', 'email', 'phone', 'longitude', 'latitude', 'currency', 'facebook', 'google_plus',
        'twitter', 'instagram', 'is_active', 'logo', 'updated_by', 'app_store_link', 'play_store_link', 'max_day_class'];

//    mapping translate
    public $translatedAttributes = ['title', 'slogan', 'keywords', 'description', 'address', 'payment_guide', 'payment_info'];


}
