<?php

namespace App\Http\Controllers\API\LandingPage;

use App\Models\LandingContent;
use App\Models\LandingPage;
use App\Models\LandingType;
use App\Service\BaseResponse;
use App\Transformers\LandingContentAdminTransformer;
use App\Transformers\LandingContentTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

class ListController extends Controller
{

    private $fractal;
    /**
     * @var LandingContentTransformer
     */
    private $contentTransformer;

    /**
     * @var LandingContentAdminTransformer
     */
    private $landingContentAdminTransformer;

    function __construct(Manager $fractal, LandingContentTransformer $contentTransformer,
                         LandingContentAdminTransformer $landingContentAdminTransformer)
    {
        $this->fractal = $fractal;
        $this->contentTransformer = $contentTransformer;
        $this->landingContentAdminTransformer = $landingContentAdminTransformer;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getListAdmin(Request $request)
    {
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $column = $request->query('column');
        $order_by_type = $request->query('order_by_type');
        $is_active = $request->get('is_active');
        $type_id = $request->get('type_id');
        $page_id = $request->get('page_id');
        $search_string = $request->query('search_string');
        $lang = $request->query('lang');
        $content_list = LandingContent::query()->with(['children', 'page', 'type'])
            ->searchBy($search_string, $lang)
            ->type($type_id)
            ->page($page_id)
            ->orderByCustom($column, $order_by_type)
            ->isActive($is_active)
            ->paginate($paging);

        $contents = new Collection($content_list->items(), $this->landingContentAdminTransformer);
        $contents->setPaginator(new IlluminatePaginatorAdapter($content_list));
        $contents = $this->fractal->createData($contents);
        return BaseResponse::customResponse(
            'Get list successfully',
            $contents->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $contents->toArray()['meta']
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */

    public function getListContent(Request $request)
    {
        $page_id = $request->query('page_id');
        $type_id = $request->query('type_id');
        $content_list = LandingContent::query()->with(['children', 'page', 'type'])
            ->join('landing_page', 'landing_page.id', '=', 'landing_content.landing_page_id')
            ->join('landing_type', 'landing_type.id', '=', 'landing_content.landing_type_id')
            ->whereNull('landing_content.parent_id')
            ->where(function ($q) use ($page_id) {
                if ($page_id) {
                    $q->where('landing_page.type', (int)$page_id);
                }
            })
            ->where(function ($q) use ($type_id) {
                if ($type_id) {
                    $q->where('landing_type.type', (int)$type_id);
                }
            })
            ->select('landing_content.*')->get();
        $contents = new Collection($content_list, $this->contentTransformer);
        $contents = $this->fractal->createData($contents);
        return BaseResponse::customResponse(
            'Get list successfully',
            $contents->toArray()['data'],
            true,
            200,
            200,
            'Success',
            []
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getListPage(Request $request)
    {
        $listPagge = LandingPage::query()->get();
        return BaseResponse::customResponse(
            'Get list successfully',
            $listPagge->toArray(),
            true,
            200,
            200,
            'Success',
            []
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getListType(Request $request)
    {
        $listType = LandingType::query()->get();
        return BaseResponse::customResponse(
            'Get list successfully',
            $listType->toArray(),
            true,
            200,
            200,
            'Success',
            []
        );
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getListContentParent(Request $request)
    {
        $listContents = LandingContent::query()->whereNull('parent_id')->get();
        $data = [];
        foreach ($listContents as $content) {
            $temp = [
                'title' => $content->title . '--' . $content->page->name,
                'id' => $content->id
            ];
            array_push($data, $temp);
        }
        return BaseResponse::customResponse(
            'Get list successfully',
            $data,
            true,
            200,
            200,
            'Success',
            []
        );
    }

}
