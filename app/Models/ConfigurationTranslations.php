<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfigurationTranslations extends Model
{
    protected $table = 'configuration_translations';
    protected $fillable = ['title', 'slogan', 'keywords', 'description', 'address', 'payment_guide', 'payment_info'];
}
