<?php


namespace App\Transformers;

use App\Models\LandingContent;
use Carbon\Carbon;
use Faker\Test\Provider\Collection;
use League\Fractal\Manager;
use League\Fractal\TransformerAbstract;

class LandingContentTransformer extends TransformerAbstract
{

    public function transform(LandingContent $content)
    {
        return [
            'parent_name' => $content->parent->title ?? null,
            'landing_type_name' => $content->type->name ?? null,
            'landing_type_type' => $content->type->type ?? null,
            'landing_page_name' => $content->page->name ?? null,
            'landing_page_type' => $content->page->type ?? null,
            'image' => $content->image && $content->image != 'null' ? media_url_web($content->image) : null,
            'url' => $content->url,
            'video' => $content->video,
            'register_time' => $content->register_time ? Carbon::parse($content->register_time)->format('d-M-Y H:m:s') : null,
            'sort' => $content->sort,
            'translations' => $content->getTranslationsArray(),
            'children' => $this->transformChild($content),
        ];
    }

    public function transformChild($content)
    {
        $data = [];
        foreach ($content->children as $key => $value) {
            $data[] = $this->transform($value);
        }
        return $data;
    }
}
