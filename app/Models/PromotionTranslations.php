<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionTranslations extends Model
{
    protected $table = 'promotion_translations';
    protected $fillable = ['promotion_id', 'locale', 'name', 'description'];
}
