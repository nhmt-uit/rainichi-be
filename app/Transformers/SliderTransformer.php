<?php
/**
 * Created by PhpStorm.
 * User: thachnguyen
 */

namespace App\Transformers;

use App\Models\Slider;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class SliderTransformer extends TransformerAbstract
{

    public function transform(Slider $slider)
    {
        return [
            'id' => $slider->id,
            'category_id' => $slider->category_id,
            'image' => $slider->image ? media_url_web( $slider->image) : null,
            'sort_order' => $slider->sort_order ,
            'translations' => $slider->getTranslationsArray(),
            'link_to' => $slider->link_to,
            'link_to_type' => $slider->link_to_type,
            'type' => $slider->type,
            'platform' => $slider->platform,
            'is_active' => $slider->is_active,
            'created_at' => Carbon::parse($slider->created_at)->format('d-m-Y'),
            'created_by' => $slider->user ? $slider->user->name : ''
        ];
    }
}
