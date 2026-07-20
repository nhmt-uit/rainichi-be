<?php

namespace App\Http\Controllers\API\Category;

use Illuminate\Support\Str;
use App\Models\Category;
use App\Service\BaseResponse;
use App\Transformers\ArticleAdminTransformer;
use App\Transformers\CategoryAdminTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class UpdateController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var articleAdminTransformer
     */
    private $categoryAdminTransformer;

    function __construct(Manager $fractal, CategoryAdminTransformer $categoryAdminTransformer)
    {
        $this->fractal = $fractal;
        $this->categoryAdminTransformer = $categoryAdminTransformer;
    }

    public function update(Request $request)
    {
        if ($request->isMethod('patch')) {
            $category_id = $request->route('id');
            $data_change = $request->except('_method');
            $data_change = $request->all();
            $category = Category::find($category_id);
            if($category){
                if ($request->translations) {
                    $language_keys = array_keys($data_change['translations']);
                    foreach ($language_keys as $language) {
                        $category->translateOrNew($language)->name = $data_change['translations'][$language]['name'];
                        $category->translateOrNew($language)->short_content = $data_change['translations'][$language]['short_content'];
                        if(key_exists('slug', $data_change['translations'][$language])) {
                            $category->translateOrNew($language)->slug = Str::slug($data_change['translations'][$language]['slug']);
                        }
                    }
                }

                $data_change['updated_by'] = Auth::user()->id;
                $category->update($data_change);

                if ($category->save()) {
                    return BaseResponse::customResponse(
                        'Update category successfully',
                        (new CategoryAdminTransformer)->transform($category),
                        true,
                        200,
                        200,
                        'Success'
                    );
                }
            }
            else
            {
                return BaseResponse::customResponse(
                    'Data not found',
                    [],
                    false,
                    Config('error_constant.article.not_found'),
                    404,
                    'NotFound'
                );
            }
        }
    }

    public function detail(Request $request){
        $article_id = $request->route('id');
        $article = Category::query()->find($article_id);
        if($article){
            $article = new Item($article, $this->categoryAdminTransformer);
            $article = $this->fractal->createData($article);
            return BaseResponse::customResponse(
                'Get details data successfully',
                $article->toArray()['data'],
                true,
                200,
                200,
                'Success'
            );
        }
        else {
            return BaseResponse::customResponse(
                'Data not found',
                [],
                false,
                Config('error_constant.article.not_found'),
                404,
                'NotFound'
            );
        }
    }
}
