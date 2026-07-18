<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventDayTranslations extends Model
{
    protected $table = 'event_day_translations';

    public $timestamps = false;

    protected $fillable = ['name'];
}
