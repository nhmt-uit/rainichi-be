<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;


use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class NotificationAdminTransformer extends TransformerAbstract
{

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var NotificationAdminTransformer
     */
    private $coursePrice;
    /**
     * @var NotificationAdminTransformer
     */
    private $notificationRouteTransformer;

    function __construct(Manager $fractal)
    {
        $this->fractal = $fractal;
    }

    public function transform(Notification $notification)
    {
        return [
            'id' => $notification->id,
            'execute_date' => $notification->execute_date,
            'is_daily' => $notification->is_daily,
            'is_weekdaily' => $notification->is_weekdaily,
            'days_of_week' => $notification->days_of_week,
            'hours' => $notification->hours,
            'minute' => $notification->minute,
            'status' => $notification->status,
            'type' => Notification::getNotificationType($notification->type),
            'title' => $notification->title,
            'description' => $notification->description,
            'translations' => $notification->getTranslationsArray(),
            'created_at' => Carbon::parse($notification->created_at)->format('Y-m-d H:i:s'),
            'users' => $notification->notification_user_list,
            'send_to' => Notification::getNotificationSendTo($notification->send_to),
            'group' => $notification->group_id ? $notification->group : null
        ];
    }    
}
