<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingContentTranslations extends Model
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'id',
        'landing_content_id',
        'locale',
        'title',
        'sub_title',
        'start_date',
        'time_range',
        'address',
        'short_content',
        'content',
    ];
}
