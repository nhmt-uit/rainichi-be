<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 03/12/2018
 * Time: 11:50
 */

namespace App\Http\Controllers\API\Reading;


use App\Http\Controllers\Controller;
use App\Models\LessonVocabulary;
use App\Models\Reading;
use App\Models\ReadingGroup;
use App\Service\BaseResponse;
use App\Transformers\ReadingTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

class ListController extends Controller
{
    const VOCABULARY = 1;
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var ReadingTransformer
     */
    private $readingTransformer;


    function __construct(Manager $fractal, ReadingTransformer $readingTransformer)
    {
        $this->fractal = $fractal;
        $this->readingTransformer = $readingTransformer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $paging = $request->query('limit') ? $request->query('limit') : 15;
        $reading_list = Reading::query()->readingActive(true)->with('levels')->paginate($paging);

        $readings = new Collection($reading_list->items(), $this->readingTransformer);
        $readings->setPaginator(new IlluminatePaginatorAdapter($reading_list));
        $readings = $this->fractal->createData($readings);
        return BaseResponse::customResponse(
            'Get list successfully',
            $readings->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $readings->toArray()['meta']
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
        $levels = $request->query('levels');
        $column = $request->query('column');
        $is_active = $request->get('is_active');
        $order_by_type = $request->query('order_by_type');
        $search_string = $request->query('search_string');
        $lang = $request->query('lang');
        $reading_list = Reading::with('levels', 'user')
            ->searchBy($search_string, $lang)
            ->searchByCreatedBy($created_by)
            ->searchByLevel($levels)
            ->orderByCustom($column, $order_by_type)
            ->readingActive($is_active)
            ->paginate($paging);

        $readings = new Collection($reading_list->items(), $this->readingTransformer);
        $readings->setPaginator(new IlluminatePaginatorAdapter($reading_list));
        $readings = $this->fractal->createData($readings);
        return BaseResponse::customResponse(
            'Get list successfully',
            $readings->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $readings->toArray()['meta']
        );
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListInLesson($id)
    {
        $group_chapter_id = LessonVocabulary::query()->where('lesson_id', $id)->pluck('group_chapter_id');
        if ($group_chapter_id) {
            $reading_id = ReadingGroup::query()->whereIn('group_chapter_id', $group_chapter_id)->pluck('reading_id');

            $reading = Reading::query()->with( 'questions.answer')->readingActive(true)->whereIn('id', $reading_id)->paginate(15);

            $readings = new Collection($reading->items(), $this->readingTransformer);
            $readings->setPaginator(new IlluminatePaginatorAdapter($reading));
            $readings = $this->fractal->createData($readings);
            return BaseResponse::customResponse(
                'Get list successfully',
                $readings->toArray()['data'],
                true,
                200,
                200,
                'Success',
                $readings->toArray()['meta']
            );
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListInGroup(Request $request)
    {
        $reading_id = ReadingGroup::query()->where('group_chapter_id', $request->route('group_id'))->pluck('reading_id');
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $created_by = $request->query('created_by');
        $levels = $request->query('levels');
        $column = $request->query('column');
        $order_by_type = $request->query('order_by_type');
        $reading_list = Reading::query()
            ->whereIn('id', $reading_id)
            ->with('levels', 'user')
            ->searchByCreatedBy($created_by)
            ->searchByLevel($levels)
            ->orderByCustom($column, $order_by_type)
            ->paginate($paging);
        $reading = new Collection($reading_list->items(), $this->readingTransformer);
        $reading->setPaginator(new IlluminatePaginatorAdapter($reading_list));
        $reading = $this->fractal->createData($reading);
        return BaseResponse::customResponse(
            'Get list successfully',
            $reading->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $reading->toArray()['meta']
        );
    }
}
