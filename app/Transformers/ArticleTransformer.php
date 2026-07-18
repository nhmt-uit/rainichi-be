<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use League\Fractal\TransformerAbstract;

class ArticleTransformer extends TransformerAbstract
{

    public function transform(Article $article)
    {
        return [
            'id' => $article->id,
            'category_id' => $article->category_id,
            'image' => $article->image ? media_url_web($article->image) : null,
            'view' => $article->view,
            'translations' => $article->getTranslationsArray(),
            'expired_at' => $article->expired_at,
            'offer_to' => $article->offer_to,
            'is_apply' => Auth::user() ? $article->isUserApply(Auth::user()->id) : false,
            'type' => $article->type,
            'created_at' => Carbon::parse($article->created_at)->format('d-m-Y'),
        ];
    }
}
