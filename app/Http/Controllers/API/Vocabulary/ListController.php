<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 03/12/2018
 * Time: 11:50
 */

namespace App\Http\Controllers\API\Vocabulary;


use App\Http\Controllers\Controller;
use App\LessonChapter;
use App\Models\Chapter;
use App\Models\GroupChapter;
use App\Models\LessonVocabulary;
use App\Models\Vocabulary;
use App\Models\VocabularyGroup;
use App\Service\BaseResponse;
use App\Transformers\VocabularyAdminTransformer;
use App\Transformers\VocabularyTransformer;
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
     * @var VocabularyTransformer
     */
    private $vocabularyTransformer;

    /**
     * @var VocabularyAdminTransformer
     */
    private $vocabularyAdminTransformer;

    function __construct(Manager $fractal, VocabularyTransformer $vocabularyTransformer, VocabularyAdminTransformer $vocabularyAdminTransformer)
    {
        $this->fractal = $fractal;
        $this->vocabularyTransformer = $vocabularyTransformer;
        $this->vocabularyAdminTransformer = $vocabularyAdminTransformer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $paging = $request->query('limit') ? $request->query('limit') : 15;
        $vocabulary_list = Vocabulary::vocabularyactive($request->query('is_active'))->with('levels')->paginate($paging);

        $vocabularies = new Collection($vocabulary_list->items(), $this->vocabularyTransformer);
        $vocabularies->setPaginator(new IlluminatePaginatorAdapter($vocabulary_list));
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
        $vocabulary_list = Vocabulary::with('levels', 'user')
            ->searchBy($search_string, $lang)
            ->searchByCreatedBy($created_by)
            ->searchByLevel($levels)
            ->orderByCustom($column, $order_by_type)
            ->vocabularyActive($is_active)
            ->paginate($paging);

        $vocabularies = new Collection($vocabulary_list->items(), $this->vocabularyAdminTransformer);
        $vocabularies->setPaginator(new IlluminatePaginatorAdapter($vocabulary_list));
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

    public function getListInLesson(Request $request, $id)
    {
        $group_chapter_id = LessonVocabulary::query()->where('lesson_id', $id)->pluck('group_chapter_id');
        if ($group_chapter_id) {
            $paging = $request->query('per_page') ? $request->query('per_page') : 15;
            $vocabulary_id = VocabularyGroup::query()->whereIn('group_chapter_id', $group_chapter_id)->pluck('vocabulary_id');

            $vocabulary = Vocabulary::query()
                ->whereIn('id', $vocabulary_id)
                ->where('is_active', true)->paginate($paging);

            $vocabularies = new Collection($vocabulary->items(), $this->vocabularyTransformer);
            $vocabularies->setPaginator(new IlluminatePaginatorAdapter($vocabulary));
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
}
