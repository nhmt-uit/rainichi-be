<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\EventDay;
use League\Fractal\TransformerAbstract;

class EventTransformer extends TransformerAbstract
{

    public function transform(EventDay $eventDay)
    {
        return [
            'credits' => $eventDay->credits,
            'group' => $eventDay->group_id ? $eventDay->group : null,
            'type' => $eventDay->type,
            'name' => $eventDay->name,
            'translations' => $eventDay->getTranslationsArray(),
            'created_at' => convertAsiaDate($eventDay->created_at),
            'users' => $eventDay->users
        ];
    }
}
