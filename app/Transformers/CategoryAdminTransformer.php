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

class CategoryAdminTransformer extends TransformerAbstract
{

    public function transform(Category $category)
    {
        return [
            'id' => $category->id,
            'parent_id' => $category->parent_id,
            'link_to' => $category->link_to,
            'type' => $category->type,
            'sort' => $category->sort,
            'position' => $category->position,
            'is_default' => $category->is_default,
            'translations' => $category->getTranslationsArray(),
            'is_active' => $category->is_active,
            'created_at' => Carbon::parse($category->created_at)->format('d-m-Y'),
            'updated_at' => Carbon::parse($category->updated_at)->format('d-m-Y'),
            'created_by' => $category->user ? $category->user->name : null,
        ];
    }
}
