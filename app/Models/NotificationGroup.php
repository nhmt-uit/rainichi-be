<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationGroup extends Model
{
    public $timestamps = true;

    public $table = 'notification_group';

    protected $fillable = ['user_id', 'notification_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function notificationRef()
    {
        return $this->belongsTo('App\Models\Notification');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userRef()
    {
        return $this->belongsTo('App\Model\Users');
    }
}
