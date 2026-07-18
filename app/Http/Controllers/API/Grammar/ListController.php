<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Grammar;


use App\Http\Controllers\Controller;
use App\Models\Grammar;
use App\Models\GrammarGroup;
use App\Models\LessonVocabulary;
use App\Service\BaseResponse;
use App\Transformers\GrammarTransformer;
use Illuminate\Http\Request;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

class ListController extends Controller
{

    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var GrammarTransformer
     */
    private $grammarTransformer;

    function __construct(Manager $fractal, GrammarTransformer $grammarTransformer)
    {
        $this->fractal = $fractal;
        $this->grammarTransformer = $grammarTransformer;
    }

    /**
     * List all grammar
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $created_by = $request->query('created_by');
        $levels = $request->query('levels');
        $column = $request->query('column');
        $order_by_type = $request->query('order_by_type');
        $lang = $request->query('lang');
        $search_string = $request->query('search_string');
        $grammar_list = Grammar::with(['level', 'sentences', 'user'])
            ->searchByString($search_string, $lang)
            ->searchByCreatedBy($created_by)
            ->searchByLevel($levels)
            ->orderByCustom($column, $order_by_type)
            ->orderByName($column, $order_by_type, $lang)
            ->paginate($paging);

        $grammars = new Collection($grammar_list->items(), $this->grammarTransformer);
        $grammars->setPaginator(new IlluminatePaginatorAdapter($grammar_list));
        $grammars = $this->fractal->createData($grammars);

        return BaseResponse::customResponse(
            'Get list successfully',
            $grammars->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $grammars->toArray()['meta']
        );
    }

    /**
     * @param $lesson_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListInLesson(Request$request, $lesson_id)
    {
        $group_chapter_id = LessonVocabulary::query()->where('lesson_id', $lesson_id)->pluck('group_chapter_id');
        if ($group_chapter_id) {
            $paging = $request->query('per_page') ? $request->query('per_page') : 15;
            $grammar_id = GrammarGroup::query()->whereIn('group_chapter_id', $group_chapter_id)->pluck('grammar_id');

            $conversation = Grammar::query()->whereIn('id', $grammar_id)->where('is_active', true)->paginate($paging);

            $vocabularies = new Collection($conversation->items(), $this->grammarTransformer);
            $vocabularies->setPaginator(new IlluminatePaginatorAdapter($conversation));
            $vocabularies = $this->fractal->createData($vocabularies);
            return BaseResponse::customResponse(
                'Get list successfully',
                $vocabularies->toArray()['data'],
                true,
                200,
                200,
                'Success',
                $vocabularies->toArray()['meta']
            );
        }


    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListInGroup(Request $request)
    {
        $grammar_id = GrammarGroup::query()->where('group_chapter_id', $request->route('group_id'))->pluck('grammar_id');
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $created_by = $request->query('created_by');
        $levels = $request->query('levels');
        $column = $request->query('column');
        $order_by_type = $request->query('order_by_type');
        $search_string = $request->query('search_string');
        $lang = $request->query('lang');
        $conversation_list = Grammar::query()
            ->whereIn('id', $grammar_id)
            ->with('level', 'user', 'sentences')
            ->searchByString($search_string, $lang)
            ->searchByCreatedBy($created_by)
            ->searchByLevel($levels)
            ->orderByCustom($column, $order_by_type)
            ->paginate($paging);
        $vocabularies = new Collection($conversation_list->items(), $this->grammarTransformer);
        $vocabularies->setPaginator(new IlluminatePaginatorAdapter($conversation_list));
        $vocabularies = $this->fractal->createData($vocabularies);
        return BaseResponse::customResponse(
            'Get list successfully',
            $vocabularies->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $vocabularies->toArray()['meta']
        );
    }
}
