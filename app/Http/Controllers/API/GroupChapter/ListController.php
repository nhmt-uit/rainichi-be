<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 03/12/2018
 * Time: 11:50
 */

namespace App\Http\Controllers\API\GroupChapter;


use App\Http\Controllers\Controller;
use App\Models\GroupChapter;
use App\Models\LessonVocabulary;
use App\Models\Vocabulary;
use App\Models\VocabularyGroup;
use App\Service\BaseResponse;
use App\Transformers\GroupChapterTransformer;
use App\Transformers\VocabularyAdminTransformer;
use App\Transformers\VocabularyTransformer;
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
     * @var GroupChapterTransformer
     */
    private $groupChapterTransformer;

    /**
     * @var VocabularyTransformer
     */
    private $vocabularyTransformer;

    /**
     * @var VocabularyAdminTransformer
     */
    private $vocabularyAdminTransformer;

    function __construct(Manager $fractal, GroupChapterTransformer $groupChapterTransformer, VocabularyTransformer $vocabularyTransformer, VocabularyAdminTransformer $vocabularyAdminTransformer)
    {
        $this->fractal = $fractal;
        $this->groupChapterTransformer = $groupChapterTransformer;
        $this->vocabularyTransformer = $vocabularyTransformer;
        $this->vocabularyAdminTransformer = $vocabularyAdminTransformer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $lesson = $request->get('lesson_id');
        $type = $request->get('type');
        $group_chapter = [];
        // define group_chapter null, and if has_lesson = true will show by lesson, false and group_chapter = null will show all.
        $has_lesson = false;
        if (isset($lesson)) {
            $group_chapter = LessonVocabulary::query()->where('lesson_id', $lesson)->pluck('group_chapter_id');
            $has_lesson = true;
        }
        $search_string = $request->query('search_string');
        $created_by = $request->query('created_by');
        $levels = $request->query('levels');
        $chapter_id = $request->query('chapter_id');
        $lang = $request->query('lang');
        $column = $request->query('column');
        $order_by_type = $request->query('order_by_type');
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $group_chapter = GroupChapter::with(['levels', 'createdBy', 'chapter'])
            ->searchByType($type)
            ->searchByString($search_string, $lang)
            ->getByLesson($group_chapter, $has_lesson)
            ->searchByCreatedBy($created_by)
            ->searchByLevel($levels)
            ->searchByChapter($chapter_id)
            ->orderByCustom($column, $order_by_type)
            ->orderByName($column, $order_by_type, $lang)
            ->paginate($paging);

        $g_chapter = new Collection($group_chapter->items(), $this->groupChapterTransformer);
        $g_chapter->setPaginator(new IlluminatePaginatorAdapter($group_chapter));
        $g_chapter = $this->fractal->createData($g_chapter);
        return BaseResponse::customResponse(
            'Get list successfully',
            $g_chapter->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $g_chapter->toArray()['meta']
        );
    }

    public function listVocabulary(Request $request)
    {
        $vocabulary_id = VocabularyGroup::query()->where('group_chapter_id', $request->route('id'))->pluck('vocabulary_id');
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $created_by = $request->query('created_by');
        $levels = $request->query('levels');
        $column = $request->query('column');
        $order_by_type = $request->query('order_by_type');
        $search_string = $request->query('search_string');
        $lang = $request->query('lang');
        $vocabulary_list = Vocabulary::query()
            ->whereIn('id', $vocabulary_id)
            ->with('levels', 'user')
            ->searchByCreatedBy($created_by)
            ->searchBy($search_string, $lang)
            ->searchByLevel($levels)
            ->orderByCustom($column, $order_by_type)
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
}
