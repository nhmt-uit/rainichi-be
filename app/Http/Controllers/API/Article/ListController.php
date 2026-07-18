<?php

/**
 * User: Thach Nguyen
 * Date: 21/06/2019
 */

namespace  App\Http\Controllers\API\Article;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Service\BaseResponse;
use App\Transformers\ArticleTransformer;
use App\Transformers\ArticleAdminTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
class  ListController extends Controller
{
    private $fractal;
    /**
     * @var ArticleTransformer
     */
    private $articleTransformer;
    private $articleAdminTransformer;

    function __construct(Manager $fractal, ArticleTransformer $articleTransformer, ArticleAdminTransformer $articleAdminTransformer) {
        $this->fractal = $fractal;
        $this->articleTransformer = $articleTransformer;
        $this->articleAdminTransformer = $articleAdminTransformer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListAdmin(Request $request)
    {
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $created_by = $request->query('created_by');
        $column = $request->query('column') ?? 'id';
        $order_by_type = $request->query('order_by_type') ?? 'desc';
        $is_active = $request->get('is_active');
        $category_id = $request->get('category_id');
        $search_string = $request->query('search_string');
        $lang = $request->query('lang');
        $article_list = Article::with( 'user')
            ->searchBy($search_string, $lang)
            ->searchByCreatedBy($created_by)
            ->orderByCustom($column, $order_by_type)
            ->orderByName($column, $order_by_type, $lang)
            ->isActive($is_active)
            ->categoryId($category_id)
            ->paginate($paging);

        $articles = new Collection($article_list->items(), $this->articleAdminTransformer);
        $articles->setPaginator(new IlluminatePaginatorAdapter($article_list));
        $articles = $this->fractal->createData($articles);
        return BaseResponse::customResponse(
            'Get list successfully',
            $articles->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $articles->toArray()['meta']
        );
    }

    public function getCategory()
    {
        $typeList = Article::getArticleType();
        return BaseResponse::customResponse(
            'Get list successfully',
            $typeList,
            true,
            200,
            200,
            'Success'
        );
    }


}
