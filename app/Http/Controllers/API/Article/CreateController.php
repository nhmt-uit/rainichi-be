<?php


namespace App\Http\Controllers\API\Article;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use App\Service\BaseResponse;
use App\Service\UploadService;
use App\Transformers\ArticleAdminTransformer;
use Illuminate\Support\Facades\Auth;


class CreateController extends  Controller {

    /**
     *Add new news
     *@params ArticleRequest $request
     * return \Illuminate\Http\JsonResponse
     */

    public function create(ArticleRequest $request)
    {
        $article_data = $request->all();

        //check image
        if ($request->hasFile('image')) {

            $image = UploadService::handleUploadFile($request->file('image'), Config('uploadpath.article_image_folder'));
            $article_data['image'] = $image;
        }

        $article_data['created_by'] = Auth::user()->id;
        $article = Article::create($article_data);

        // get translation keys and add to translation table
        $language_keys = array_keys($article_data['translations']);
        foreach ($language_keys as $language) {
            $article->translateOrNew($language)->name = $article_data['translations'][$language]['name'];
            $article->translateOrNew($language)->short_content = $article_data['translations'][$language]['short_content'] ?? '';
            $article->translateOrNew($language)->content = $article_data['translations'][$language]['content'] ?? '';
            $article->translateOrNew($language)->seo_name = $article_data['translations'][$language]['seo_name'] ?? '';
            $article->translateOrNew($language)->seo_keywords = $article_data['translations'][$language]['seo_keywords'] ?? '';
            $article->translateOrNew($language)->seo_content = $article_data['translations'][$language]['seo_content'] ?? '';
            $article->translateOrNew($language)->position = $article_data['translations'][$language]['position'] ?? '';
            $article->translateOrNew($language)->work_at = $article_data['translations'][$language]['work_at'] ?? '';
            $article->translateOrNew($language)->offer = $article_data['translations'][$language]['offer'] ?? '';
            if (key_exists('slug', $article_data['translations'][$language])) {
                $article->translateOrNew($language)->slug = Str::slug($article_data['translations'][$language]['slug'], '-');
            } else {
                $article->translateOrNew($language)->slug = Str::slug($article_data['translations'][$language]['name'], '-');

            }
        }

        if ($article->save()) {
            return BaseResponse::customResponse(
                'Create article successfully',
                (new ArticleAdminTransformer)->transform($article),
                true,
                200,
                201,
                'Created'
            );
        }else {
            return BaseResponse::customResponse(
                'Fail to insert new article',
                [],
                false,
                Config('error_constant.article.insert_fail'),
                422,
                'Unprocessable Entity'
            );
        }
    }





}
