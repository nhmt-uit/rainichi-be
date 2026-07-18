<?php


namespace App\Transformers;

use App\Models\Article;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class ArticleAdminTransformer extends TransformerAbstract
{

    public function transform(Article $article)
    {
        return [
            'id' => $article->id,
            'category_id' => $article->category_id,
            'image' => $article->image && $article->image != 'null' ? media_url_web($article->image) : null,
            'translations' => $article->getTranslationsArray(),
            'type' => $article->type,
            'view' => $article->view ,
            'sort' => $article->sort ,
            'share_facebook' => $article->share_facebook,
            'share_twitter' => $article->share_twitter,
            'share_google' => $article->share_google,
            'expired_at' => empty($article->expired_at) ? null : Carbon::parse($article->expired_at)->format('d-m-Y') ,
            'offer_to' => $article->offer_to,
            'is_active' => $article->is_active,
            'created_at' => Carbon::parse($article->created_at)->format('d-m-Y'),
            'updated_at' => Carbon::parse($article->updated_at)->format('d-m-Y'),
            'created_by' => $article->user ? $article->user->name : null,
            'updated_by' => $article->user ? $article->user->name : null,
        ];
    }
}
