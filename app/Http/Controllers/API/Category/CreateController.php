<?php

namespace App\Http\Controllers\API\Category;

use Illuminate\Support\Str;
use App\Models\Category;
use App\Service\BaseResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{
    public function create(Request $request)
    {
        $category_data = $request->all();
        $category_data['created_by'] = Auth::user()->id;
        $category = Category::create($category_data);
        $language_keys = array_keys($category_data['translations']);
        foreach ($language_keys as $language) {
            $category->translateOrNew($language)->name = $category_data['translations'][$language]['name'];
            $category->translateOrNew($language)->short_content = $category_data['translations'][$language]['short_content'];
            if(key_exists('slug', $category_data['translations'][$language])) {
                $category->translateOrNew($language)->slug = Str::slug($category_data['translations'][$language]['slug'], '-');
            }else {
                $category->translateOrNew($language)->slug = Str::slug($category_data['translations'][$language]['name'], '-');
            }
        }

        if ($category->save()) {
            return BaseResponse::customResponse(
                'Create category successfully',
                $category,
                true,
                200,
                201,
                'Created'
            );
        }else {
            return BaseResponse::customResponse(
                'Fail to insert new category',
                [],
                false,
                Config('error_constant.normal.insert_fail'),
                422,
                'Unprocessable Entity'
            );
        }
    }
}
