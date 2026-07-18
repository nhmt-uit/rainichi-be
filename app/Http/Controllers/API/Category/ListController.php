<?php

namespace App\Http\Controllers\API\Category;

use App\Models\Article;
use App\Service\BaseResponse;
use App\Transformers\ArticleAdminTransformer;
use App\Transformers\ArticleTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;
use App\Transformers\CategoryTransformer;
use App\Transformers\CategoryAdminTransformer;
use App\Models\Category;
use App\Transformers\SliderTransformer;

class ListController extends Controller
{
    private $fractal;
    private $categoryTransformer;
    private $categoryAdminTransformer;
    private $articleTransformer;
    private $sliderTransformer;

    function __construct(Manager $fractal, CategoryTransformer $categoryTransformer, CategoryAdminTransformer $categoryAdminTransformer,
                         ArticleTransformer $articleTransformer, SliderTransformer $sliderTransformer)
    {
        $this->fractal = $fractal;
        $this->categoryTransformer = $categoryTransformer;
        $this->categoryAdminTransformer = $categoryAdminTransformer;
        $this->articleTransformer = $articleTransformer;
        $this->sliderTransformer = $sliderTransformer;
    }


    public function getList()
    {

        $headerMenu = Category::getPosition([Category::POS_HEADER, Category::POS_ALL]);
        $footerMenu = Category::getPosition([Category::POS_FOOTER, Category::POS_ALL]);
        $categoryHeader = new Collection($headerMenu, $this->categoryTransformer);
        $categoryHeader = $this->fractal->createData($categoryHeader);
        $categoryFooter = new Collection($footerMenu, $this->categoryTransformer);
        $categoryFooter = $this->fractal->createData($categoryFooter);
        $data = [
            'header' => $categoryHeader->toArray(),
            'footer' => $categoryFooter->toArray()
        ];
        return BaseResponse::customResponse(
            'Get list category successfully',
            $data,
            true,
            200,
            200,
            'Success'
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListAdmin(Request $request)
    {
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $created_by = $request->query('created_by');
        $column = $request->query('column');
        $order_by_type = $request->query('order_by_type');
        $is_active = $request->get('is_active');
        $search_string = $request->query('search_string');
        $lang = $request->query('lang');
        $category_list = Category::with('user')
            ->searchBy($search_string, $lang)
            ->searchByCreatedBy($created_by)
            ->orderByCustom($column, $order_by_type)
            ->isActive($is_active)
            ->paginate($paging);
        $category = new Collection($category_list->items(), $this->categoryAdminTransformer);
        $category->setPaginator(new IlluminatePaginatorAdapter($category_list));
        $category = $this->fractal->createData($category);
        return BaseResponse::customResponse(
            'Get list category successfully',
            $category->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $category->toArray()['meta']
        );
    }

    public function getListTree()
    {
        $data = Category::GetTreeCategory();
        return BaseResponse::customResponse(
            'Get list category successfully',
            $data,
            true,
            200,
            200,
            'Success'
        );
    }

    public function getArticleListByCategory(Request $request)
    {
        $type = $request->query('type') ?? null;
        $slug = $request->query('slug') ?? null;

        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $category = Category::with('articles', 'sliders')
            ->getBySlug($slug)
            ->orWhere('type', (int)$type)
            ->isActive(true)->first();
        if (($type || $slug) && $category) {
            $response_data = [
                'data' => [],
                'meta' => []
            ];
            $article_list = $category->articles()->isActive(true)->orderByDesc('created_at')->paginate($paging);
            $articles = new Collection($article_list->items(), $this->articleTransformer);
            $articles->setPaginator(new IlluminatePaginatorAdapter($article_list));
            $articles = $this->fractal->createData($articles);
            $slider_list = $category->sliders()->isActive(true)->orderByCustom('id', 'DESC')->get();
            $sliders = new Collection($slider_list, $this->sliderTransformer);
            $sliders = $this->fractal->createData($sliders);
            $response_data['data'] = [
                'articles' => $articles->toArray()['data'],
                'articleType' => intval($category->type),
                'category' => (new CategoryTransformer)->transform($category),
                'slider' =>  $sliders->toArray()['data']
            ];
            $response_data['meta'] = $articles->toArray()['meta'];
            return BaseResponse::customResponse(
                __('Get articles successfully'),
                $response_data['data'],
                true,
                200,
                200,
                'Success',
                $response_data['meta']
            );
        }
        return BaseResponse::customResponse(
            __('Data not found'),
            [],
            true,
            404,
            404,
            'Not found'
        );
    }

    public function position()
    {

        $positions = Category::getCategoryPosition();
        return BaseResponse::customResponse(
            'Get list successfully',
            $positions,
            true,
            200,
            200,
            'Success'
        );
    }
}
