<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\Conversation;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class ConversationTransformer extends TransformerAbstract
{

    public function transform(Conversation $conversation)
    {
        return [
            'id' => $conversation->id,
            'type' => $conversation->type,
            'name' => $conversation->name,
            'image' => $conversation->image ? media_url_web( $conversation->image) : null,
            'media' => $conversation->type === Conversation::AUDIO ? ($conversation->audio ? media_url_web( $conversation->audio) : null) : ($conversation->video ? media_url_web( $conversation->video) : null),
            'translations' => $conversation->getTranslationsArray(),
            'is_active' => $conversation->is_active,
            'level_id' => $conversation->level_id ? $conversation->level_id : 0,
            'level' => $conversation->levels ? $conversation->levels->name : null,
            'created_at' => Carbon::parse($conversation->created_at)->format('d-m-Y'),
            'created_by' => $conversation->user ? $conversation->user->name : null,
        ];
    }
}
