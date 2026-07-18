<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Conversation;


use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\ConversationGroup;
use App\Models\LessonVocabulary;
use App\Service\BaseResponse;
use App\Transformers\ConversationTransformer;
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
     * @var ConversationTransformer
     */
    private $conversationTransformer;

    function __construct(Manager $fractal, ConversationTransformer $conversationTransformer)
    {
        $this->fractal = $fractal;
        $this->conversationTransformer = $conversationTransformer;
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
        $order_by_type = $request->query('order_by_type');
        $lang = $request->query('lang');
        $search_string = $request->query('search_string');
        $conversation_list = Conversation::with('levels', 'user')
            ->searchByString($search_string, $lang)
            ->searchByCreatedBy($created_by)
            ->searchByLevel($levels)
            ->orderByCustom($column, $order_by_type)
            ->orderByName($column, $order_by_type, $lang)
            ->paginate($paging);

        $vocabularies = new Collection($conversation_list->items(), $this->conversationTransformer);
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

    /**
     * @param $lesson_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListInLesson(Request $request, $lesson_id)
    {
        $group_chapter_id = LessonVocabulary::query()->where('lesson_id', $lesson_id)->pluck('group_chapter_id');
        if ($group_chapter_id) {
            $paging = $request->query('per_page') ? $request->query('per_page') : 15;
            $conversation_id = ConversationGroup::query()->whereIn('group_chapter_id', $group_chapter_id)->pluck('conversation_id');

            $conversation = Conversation::query()->whereIn('id', $conversation_id)->where('is_active', true)->paginate($paging);

            $vocabularies = new Collection($conversation->items(), $this->conversationTransformer);
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
        $conversation_id = ConversationGroup::query()->where('group_chapter_id', $request->route('group_id'))->pluck('conversation_id');
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $created_by = $request->query('created_by');
        $levels = $request->query('levels');
        $column = $request->query('column');
        $order_by_type = $request->query('order_by_type');
        $lang = $request->query('lang');
        $conversation_list = Conversation::query()
            ->whereIn('id', $conversation_id)
            ->with('levels', 'user')
            ->searchByCreatedBy($created_by)
            ->searchByLevel($levels)
            ->orderByCustom($column, $order_by_type)
            ->orderByName($column, $order_by_type, $lang)
            ->paginate($paging);
        $vocabularies = new Collection($conversation_list->items(), $this->conversationTransformer);
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
