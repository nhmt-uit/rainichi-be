<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventDay extends Model
{
    const ALL = 1;
    const GROUP = 2;
    const USER_SELECTED = 3;

    use \Astrotomic\Translatable\Translatable;

    protected $table = 'event_day';

    public $timestamps = true;

    public $fillable = ['credits', 'group_id', 'type'];

    public $translatedAttributes = ['name'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'event_day_users');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(GroupUser::class, 'group_id', 'id');
    }

}
