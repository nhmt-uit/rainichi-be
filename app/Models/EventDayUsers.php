<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventDayUsers extends Model
{
    protected $table = 'event_day_users';

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function eventDay()
    {
        return $this->belongsTo(EventDay::class, 'event_day_id', 'id');
    }
}
