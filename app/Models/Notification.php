<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = true;

    use \Astrotomic\Translatable\Translatable;

    protected $table = 'notification';

    protected $fillable = ['type', 'execute_date', 'status', 'days_of_week', 'hours', 'minute', 'send_to', 'group_id'];

    protected $casts = ['status' => 'boolean'];

    public $translatedAttributes = ['title', 'description'];

    const NOTIFICATION_TYPE = ['DAILY' => 1, 'WEEKLY' => 2, 'ONETIME' => 3];

    const NOTIFICATION_TEMPLATE = ['DAILY' => "%s  %s  *  *  *", 'WEEKLY' => "%s  %s  *  *  %s", 'ONETIME' => "%s %s  *"];

    const NOTIFICATION_SEND_TO = ['OTHER' => 0, 'ALL' => 1, 'GROUP' => 2, 'LIST_USER' => 3];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(GroupUser::class, 'group_id', 'id');
    }

    /**
     * @param $q
     * @param $search_string
     * @param $lang
     * @return mixed
     */
    public function scopeSearchString($q, $search_string, $lang)
    {
        if (isset($search_string) && isset($lang)) {
            return $q->whereHas(
                'translations',
                function ($query) use ($search_string, $lang) {
                    $query->where('title', 'like', '%' . $search_string . '%');
                    $query->where('locale', $lang);
                }
            );
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function notification_user_list()
    {
        return $this->belongsToMany(User::class, 'notification_group');
    }

    /*
    *type is key text
    */
    static function notification_type($type)
    {
        if ($type && array_key_exists($type, self::NOTIFICATION_TYPE)) {
            $type = self::NOTIFICATION_TYPE[$type];
        } else {
            $type = 1;
        }
        return $type;
    }

    static function getNotificationType($type)
    {
        $type = array_keys(Notification::NOTIFICATION_TYPE, $type);
        return $type[0];
    }

    /*
    *send to is key text
    */
    static function notification_send_to($value)
    {
        if ($value && array_key_exists($value, self::NOTIFICATION_SEND_TO)) {
            $data = self::NOTIFICATION_SEND_TO[$value];
        } else {
            $data = 1;
        }
        return $data;
    }

    static function getNotificationSendTo($value)
    {
        $data = array_keys(Notification::NOTIFICATION_SEND_TO, $value);
        return $data[0];
    }

}
