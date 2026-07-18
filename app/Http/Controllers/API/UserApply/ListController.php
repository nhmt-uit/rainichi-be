<?php

namespace App\Http\Controllers\API\UserApply;

use App\Models\Article;
use App\Models\Category;
use App\Models\UserApply;
use App\Service\BaseResponse;
use App\Transformers\ArticleAdminTransformer;
use App\Transformers\UserApplyAdminTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

class ListController extends Controller
{
    private $fractal;

    /**
     * @var $applyAdminTransformer
     */

    private $applyAdminTransformer;

    /**
     * @var ArticleAdminTransformer
     */
    private $articleTransformer;

    /**
     * ListController constructor.
     * @param Manager $fractal
     * @param UserApplyAdminTransformer $applyAdminTransformer
     * @param ArticleAdminTransformer $articleTransformer
     */
    public function __construct(Manager $fractal, UserApplyAdminTransformer $applyAdminTransformer, ArticleAdminTransformer $articleTransformer)
    {
        $this->fractal = $fractal;
        $this->applyAdminTransformer = $applyAdminTransformer;
        $this->articleTransformer = $articleTransformer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListApply(Request $request)
    {
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $updated_by = $request->query('updated_by');
        $column = $request->query('column') ?? 'id';
        $order_by_type = $request->query('order_by_type') ?? 'desc';
        $is_view = $request->get('is_view');
        $article_id = $request->get('article_id');
        $search_string = $request->query('search_string');
        $user_applies = UserApply::with( 'user', 'article')
            ->searchByColumn($column, $search_string)
            ->searchByUpdatedBy($updated_by)
            ->orderByCustom($column, $order_by_type)
            ->isView($is_view)
            ->getArticle($article_id)
            ->paginate($paging);

        $applies = new Collection($user_applies->items(), $this->applyAdminTransformer);
        $applies->setPaginator(new IlluminatePaginatorAdapter($user_applies));
        $applies = $this->fractal->createData($applies);
        return BaseResponse::customResponse(
            'Get list successfully',
            $applies->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $applies->toArray()['meta']
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getJobList(Request $request){
        $article_list = Article::query()
            ->join('category', 'category.id', '=', 'articles.category_id')
            ->where('category.type', Category::JOB)
            ->where('category.is_active', true)
            ->where('articles.is_active', true)->select('articles.*')->get();
        $articles = new Collection($article_list, $this->articleTransformer);
        $articles = $this->fractal->createData($articles);
        return BaseResponse::customResponse(
            'Get list successfully',
            $articles->toArray()['data'],
            true,
            200,
            200,
            'Success',
            []
        );
        }


}
