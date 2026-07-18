<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Slider;


use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Slider;
use App\Service\BaseResponse;
use App\Transformers\SliderTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

class ListController extends Controller
{
    private $fractal;
    /**
     * @var ArticleTransformer
     */
    private $sliderTransformer;

    function __construct(Manager $fractal, SliderTransformer $sliderTransformer)
    {
        $this->fractal = $fractal;
        $this->sliderTransformer = $sliderTransformer;
    }

    public function index(Request $request)
    {
        $platform = $request->query('platform');
        $type = $request->query('type');
        $response_data = [
            'data' => [],
            'meta' => []
        ];
        if (isset($type) && in_array($platform, Slider::allowPlatformAccess()) && in_array($type, Slider::allowTypeAccess())) {
            $sliders = Slider::query()
                ->whereNull('category_id')
                ->type($type)
                ->platform($platform)
                ->isActive(true)
                ->orderBy('sort_order')->get();
            $sliders = new Collection($sliders, $this->sliderTransformer);
            $sliders = $this->fractal->createData($sliders);
            $response_data['data'] = $sliders->toArray()['data'];
        }

        return BaseResponse::customResponse(
            'Get data successfully',
            $response_data['data'],
            true,
            200,
            200,
            'Success',
            $response_data['meta']
        );
    }

    //only for website
    public function getByCategory(Request $request)
    {
        $slug = $request->query('slug') ?? null;
        $type = $request->query('type') ?? null;
        $response_data = [
            'data' => [],
            'meta' => []
        ];
        $category = Category::with('sliders')
            ->getBySlug($slug)
            ->isActive(true)->first();
        if ($category && $category->slug == $slug) {
            $sliders = $category->sliders()
                ->platform(Slider::WEBSITE)
                ->type($type)
                ->isActive(true)
                ->orderBy('sort_order')->get();
            $sliders = new Collection($sliders, $this->sliderTransformer);
            $sliders = $this->fractal->createData($sliders);
            $response_data['data'] = $sliders->toArray()['data'];
            return BaseResponse::customResponse(
                'Get data successfully',
                $response_data['data'],
                true,
                200,
                200,
                'Success',
                $response_data['meta']
            );
        }
        return BaseResponse::customResponse(
            'Data not found',
            [],
            true,
            404,
            404,
            'Not Found',
            []
        );

    }

    public function getListAdmin(Request $request)
    {
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $created_by = $request->query('created_by');
        $column = $request->query('column');
        $order_by_type = $request->query('order_by_type');
        $platform = $request->get('platform');
        $is_active = $request->get('is_active');
        $search_string = $request->query('search_string');
        $lang = $request->query('lang');
        $type = $request->query('type');
        $link_to_type = $request->query('link_to_type');

        $slider_list = Slider::with('user')
            ->searchBy($search_string, $lang)
            ->searchByCreatedBy($created_by)
            ->orderByCustom($column, $order_by_type)
            ->orderByName($column, $order_by_type, $lang)
            ->platform($platform)
            ->type($type)
            ->linkToType($link_to_type)
            ->isActive($is_active)
            ->paginate($paging);

        $sliders = new Collection($slider_list->items(), $this->sliderTransformer);
        $sliders->setPaginator(new IlluminatePaginatorAdapter($slider_list));
        $sliders = $this->fractal->createData($sliders);
        return BaseResponse::customResponse(
            'Get list successfully',
            $sliders->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $sliders->toArray()['meta']
        );

    }
}
