<?php


namespace App\Transformers;

use App\Models\UserApply;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class UserApplyAdminTransformer extends TransformerAbstract
{
    public function transform(UserApply $apply)
    {
        return [
            'id' => $apply->id,
            'name' => $apply->name,
            'email' => $apply->email,
            'info' => $apply->info,
            'cv' => $apply->cv && $apply->cv != 'null' ? media_url_web($apply->cv) : null,
            'cv_name' => $apply->cv_name,
            'user_agent' => $apply->user_agent,
            'user_id' => $apply->user_id,
            'article_id' => $apply->article_id,
            'user_name' => $apply->user->name ?? null,
            'article_name' => $apply->article->name ?? null,
            'is_view' => $apply->is_view,
            'is_active' => $apply->active,
            'created_at' => Carbon::parse($apply->created_at)->format('d-m-Y'),
            'updated_at' => Carbon::parse($apply->updated_at)->format('d-m-Y'),
            'updated_by' => $apply->user ? $apply->user->name : null,
        ];
    }
}
