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

class NotificationTransformer extends TransformerAbstract
{
    /**
     * @var Manager
     */
    private $fractal;

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
            'status' => $notification->status,
            'type' => array_keys(Notification::NOTIFICATION_TYPE, $notification->type),
            'translations' => $notification->getTranslationsArray()
        ];
    }
}
