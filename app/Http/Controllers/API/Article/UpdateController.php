<?php

namespace App\Http\Controllers\API\Article;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Service\BaseResponse;
use App\Service\UploadService;
use App\Transformers\ArticleAdminTransformer;
use App\Transformers\ArticleTransformer;
use Illuminate\Http\Request;
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
    private $articleAdminTransformer;

    /**
     * @param Manager $fractal
     * @param ArticleAdminTransformer $articleAdminTransformer
     */
    function __construct(Manager $fractal, ArticleAdminTransformer $articleAdminTransformer)
    {
        $this->fractal = $fractal;
        $this->articleAdminTransformer = $articleAdminTransformer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        if ($request->isMethod('patch')) {
            $article_id = $request->route('id');
            $data_change = $request->except('_method');
            $data_change = $request->all();
            $article = Article::find($article_id);
            if ($article) {
                if ($request->translations) {
                    $language_keys = array_keys($data_change['translations']);
                    foreach ($language_keys as $language) {
                        $article->translateOrNew($language)->name = $data_change['translations'][$language]['name'];
                        $article->translateOrNew($language)->short_content = $data_change['translations'][$language]['short_content'];
                        $article->translateOrNew($language)->content = $data_change['translations'][$language]['content'];
                        $article->translateOrNew($language)->seo_name = $data_change['translations'][$language]['seo_name'];
                        $article->translateOrNew($language)->seo_keywords = $data_change['translations'][$language]['seo_keywords'];
                        $article->translateOrNew($language)->seo_content = $data_change['translations'][$language]['seo_content'];
                        $article->translateOrNew($language)->position = $data_change['translations'][$language]['position'];
                        $article->translateOrNew($language)->work_at = $data_change['translations'][$language]['work_at'];
                        $article->translateOrNew($language)->offer = $data_change['translations'][$language]['offer'];
                        if (key_exists('slug', $data_change['translations'][$language])) {
                            $article->translateOrNew($language)->slug = str_slug($data_change['translations'][$language]['slug'], '-');
                        } else {
                            $article->translateOrNew($language)->slug = str_slug($data_change['translations'][$language]['name'], '-');

                        }
                    }
                }

                //check image
                if ($request->hasFile('image')) {
                    $old_image = $article->image;
                    $image = UploadService::handleUploadFile($request->file('image'), Config('uploadpath.article_image_folder'));
                    $data_change['image'] = $image;
                    if ($old_image != null) {
                        UploadService::handleRemoveFile($old_image);
                    }
                }

                $data_change['updated_by'] = Auth::user()->id;
                if (key_exists('image', $data_change)) {
                    if ($data_change['image'] == 'null')
                        $data_change['image'] = null;
                }
                $article->update($data_change);

                if ($article->save()) {
                    return BaseResponse::customResponse(
                        'Update successfully',
                        (new ArticleAdminTransformer)->transform($article),
                        true,
                        200,
                        200,
                        'Success'
                    );
                }
            } else {
                return BaseResponse::customResponse(
                    __('Data not found'),
                    [],
                    false,
                    Config('error_constant.article.not_found'),
                    404,
                    'NotFound'
                );
            }
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateView(Request $request)
    {
        if ($request->isMethod('PATCH')) {
            $article_id = $request->route('id');
            $article = Article::find($article_id);
            if ($article) {
                $data_change['view'] = $article->view + 1;
                $article->update($data_change);
                if ($article->save()) {
                    return BaseResponse::customResponse(
                        'Update view article successfully',
                        null,
                        true,
                        200,
                        200,
                        'Success'
                    );
                }
            } else {
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

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail(Request $request)
    {
        #need to implement type by category if have
        $article_id = $request->route('id');
        $article_slug = $request->query('slug');
        $is_admin = $request->route('admin');
        $article = Article::query()->getBySlug($article_slug)->orWhere('id', $article_id)->first();
        if ($article) {
            $nextItem = $article->next();
            $previousItem = $article->previous();
            if ($is_admin) {
                $articleData = (new ArticleAdminTransformer)->transform($article);
            } else {
                $articleData = [
                    'item' => (new ArticleTransformer)->transform($article),
                    'next' => $nextItem ? (new ArticleTransformer)->transform($nextItem) : null,
                    'previous' => $previousItem ? (new ArticleTransformer)->transform($previousItem) : null
                ];
            }
            return BaseResponse::customResponse(
                'Get data successfully',
                $articleData,
                true,
                200,
                200,
                'Success'
            );
        } else {
            return BaseResponse::customResponse(
                __('Data not found'),
                [],
                false,
                Config('error_constant.article.not_found'),
                404,
                'NotFound'
            );
        }
    }

}
