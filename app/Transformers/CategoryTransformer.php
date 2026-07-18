<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{

    public function transform(Category $category)
    {
        return [
            'id' => $category->id,
            'slug' => $category->slug,
            'type' => $category->type,
            'link_to' => $category->link_to,
            'sort' => $category->sort,
            'translations' => $category->getTranslationsArray()
        ];
    }
}
