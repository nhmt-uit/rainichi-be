<?php


namespace App\Transformers;

use App\Models\LandingContent;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class LandingContentAdminTransformer extends TransformerAbstract
{

    public function transform(LandingContent $content)
    {
        return [
            'id' => $content->id,
            'parent_id' => $content->parent_id,
            'parent_name' => $content->parent->title ?? null,
            'landing_type_id' => $content->landing_type_id,
            'landing_type_name' => $content->type->name ?? null,
            'landing_page_id' => $content->landing_page_id,
            'landing_page_name' => $content->page->name ?? null,
            'image' => $content->image && $content->image != 'null' ? media_url_web($content->image) : null,
            'url' => $content->url,
            'video' => $content->video,
            'translations' => $content->getTranslationsArray(),
            'sort' => $content->sort,
            'is_active' => $content->is_active,
            'register_time' => $content->register_time ? Carbon::parse($content->register_time)->format('d-M-Y H:m:s') : null,
            'created_at' => Carbon::parse($content->created_at)->format('d-m-Y'),
            'updated_at' => Carbon::parse($content->updated_at)->format('d-m-Y'),
        ];
    }
}
