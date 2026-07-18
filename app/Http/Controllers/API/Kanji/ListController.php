<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Kanji;


use App\Http\Controllers\Controller;
use App\Models\CourseLesson;
use App\Models\GroupChapter;
use App\Models\Kanji;
use App\Models\KanjiGroup;
use App\Models\LessonVocabulary;
use App\Service\BaseResponse;
use App\Transformers\GroupChapterTransformer;
use App\Transformers\KanjiAdminTransformer;
use App\Transformers\KanjiGroupTransformer;
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
     * @var KanjiAdminTransformer
     */
    private $kanjiAdminTransformer;

    /**
     * @var KanjiGroupTransformer
     */
    private $kanjiGroupTransformer;

    /**
     * @var GroupChapterTransformer
     */
    private $groupChapterTransformer;

    function __construct(Manager $fractal, KanjiAdminTransformer $kanjiAdminTransformer, GroupChapterTransformer $groupChapterTransformer,
KanjiGroupTransformer $kanjiGroupTransformer)
    {
        $this->fractal = $fractal;
        $this->kanjiAdminTransformer = $kanjiAdminTransformer;
        $this->groupChapterTransformer = $groupChapterTransformer;
        $this->kanjiGroupTransformer = $kanjiGroupTransformer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $paging = $request->query('limit') ? $request->query('limit') : 15;
        $kanji_list = Kanji::query()->where('is_active', true)->with('levels')->paginate($paging);

        $vocabularies = new Collection($kanji_list->items(), $this->kanjiAdminTransformer);
        $vocabularies->setPaginator(new IlluminatePaginatorAdapter($kanji_list));
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
        $order_by_type = $request->query('order_by_type');
        $search_string = $request->query('search_string');
        $is_active = $request->query('is_active');
        $lang = $request->query('lang');
        $kanji_list = Kanji::with('levels', 'user')
            ->searchBy($search_string, $lang)
            ->searchByCreatedBy($created_by)
            ->searchByLevel($levels)
            ->orderByCustom($column, $order_by_type)
            ->vocabularyActive($is_active)
            ->paginate($paging);

        $vocabularies = new Collection($kanji_list->items(), $this->kanjiAdminTransformer);
        $vocabularies->setPaginator(new IlluminatePaginatorAdapter($kanji_list));
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
            $kanji_id = KanjiGroup::query()->whereIn('group_chapter_id', $group_chapter_id)->pluck('kanji_id');

            $kanji = Kanji::query()->whereIn('id', $kanji_id)->paginate($paging);

            $vocabularies = new Collection($kanji->items(), $this->kanjiAdminTransformer);
            $vocabularies->setPaginator(new IlluminatePaginatorAdapter($kanji));
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
        $kanji_id = KanjiGroup::query()->where('group_chapter_id', $request->route('group_id'))->pluck('kanji_id');
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $created_by = $request->query('created_by');
        $levels = $request->query('levels');
        $column = $request->query('column');
        $order_by_type = $request->query('order_by_type');
        $lang = $request->query('lang');
        $search_string = $request->query('search_string');
        $kanji_list = Kanji::query()
            ->whereIn('id', $kanji_id)
            ->with('levels', 'user')
            ->searchByCreatedBy($created_by)
            ->searchBy($search_string, $lang)
            ->searchByLevel($levels)
            ->orderByCustom($column, $order_by_type)
            ->paginate($paging);
        $vocabularies = new Collection($kanji_list->items(), $this->kanjiAdminTransformer);
        $vocabularies->setPaginator(new IlluminatePaginatorAdapter($kanji_list));
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
     * @param $kanji
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function drawKanji($kanji)
    {
        return view('kanji-draw', [
            'kanji' => $kanji
        ]);
    }

    /**
     * @param $course_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllKanjiInView($course_id)
    {
        $lesson_ids = CourseLesson::query()
            ->where('course_id', $course_id)
            ->pluck('lesson_id');
        $group_chapter_ids = LessonVocabulary::query()
            ->whereIn('lesson_id', $lesson_ids)->pluck('group_chapter_id');
        $group_chapter_list = GroupChapter::query()->whereIn('id', $group_chapter_ids)->get();
        $group_chapter_list = new Collection($group_chapter_list, $this->groupChapterTransformer);
        $group_chapter_list = $this->fractal->createData($group_chapter_list);
        $kanji_groups = KanjiGroup::query()
            ->with(['kanji', 'groupChapter'])
            ->whereIn('group_chapter_id', $group_chapter_ids)
            ->get();
        $kanji_groups = new Collection($kanji_groups, $this->kanjiGroupTransformer);
        $kanji_groups = $this->fractal->createData($kanji_groups);
        return BaseResponse::customResponse(
            "Success",
            ['kanji' => self::array_group_by($kanji_groups->toArray()['data'], 'group_chapter_id'), 'group_by' => $group_chapter_list->toArray()['data']],
            true,
            200,
            200,
            "Success",
            []
        );
    }

    /**
     * Function that groups an array of associative arrays by some key.
     *
     * @param {String} $key Property to sort by.
     * @param {Array} $data Array that stores multiple associative arrays.
     * @return array
     */
    static function array_group_by($data, $key)
    {
        $result = array();
        foreach ($data as $val) {
            if (array_key_exists($key, $val)) {
                $result[$val[$key]][] = $val['kanji'];
            } else {
                $result[""][] = $val['kanji'];
            }
        }
        return $result;
    }
}
