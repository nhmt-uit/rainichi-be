<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Lesson;


use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\CourseLesson;
use App\Models\GroupChapter;
use App\Models\Lesson;
use App\Models\LessonVocabulary;
use App\Models\Vocabulary;
use App\Models\VocabularyGroup;
use App\Service\BaseResponse;
use App\Transformers\LessonAdminTransformer;
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
     * @var LessonAdminTransformer
     */
    private $lessonAdminTransformer;

    /**
     * @var VocabularyTransformer
     */
    private $vocabularyTransformer;

    function __construct(Manager $fractal, LessonAdminTransformer $lessonAdminTransformer, VocabularyTransformer $vocabularyTransformer)
    {
        $this->fractal = $fractal;
        $this->vocabularyTransformer = $vocabularyTransformer;
        $this->lessonAdminTransformer = $lessonAdminTransformer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $lesson_id_list = null;
        $course_id = $request->query('course_id');
        if (isset($course_id)) {
            $lesson_id_list = CourseLesson::query()->where('course_id', $course_id)->pluck('lesson_id');
        }
        $per_page = $request->get('per_page') ? $request->get('per_page') : 15;
        $level_id = $request->query('levels');
        $created_by = $request->query('created_by');
        $search_string = $request->query('search_string');
        $lang = $request->query('lang');
        $column = $request->query('column');
        $order_by_type = $request->query('order_by_type');
        $chapter_id = $request->query('chapter_id');
        $lesson_list = Lesson::with(['user'])
            ->getByLevel($level_id)
            ->getByUser($created_by)
            ->searchString($search_string, $lang)
            ->orderByCustom($column, $order_by_type)
            ->orderByName($column, $order_by_type, $lang)
            ->getByCourseId($lesson_id_list, $course_id)
            ->getByChapterId($chapter_id)
            ->paginate($per_page);
        $lessons = new Collection($lesson_list, $this->lessonAdminTransformer);
        $lessons->setPaginator(new IlluminatePaginatorAdapter($lesson_list));
        $lessons = $this->fractal->createData($lessons);
        return BaseResponse::customResponse(
            'Success',
            $lessons->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $lessons->toArray()['meta']
        );
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListSkill(Request $request)
    {
        $cat = $request->query('cat');
        $skill = Chapter::query()->where('is_active', true)->getByCat($cat)->get();
        return
            BaseResponse::customResponse(
                'Success',
                $skill,
                true,
                200,
                200,
                'Success'
            );
    }

    public function getListVocabulary(Request $request, $id)
    {
        $vocabulary_type = GroupChapter::query()->where('chapter_id', self::VOCABULARY)->pluck('id');
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $lesson_vocabulary = LessonVocabulary::query()
            ->where('lesson_id', $id)
            ->whereIn('group_chapter_id', $vocabulary_type)
            ->pluck('group_chapter_id');
        $vocabulary_id = VocabularyGroup::query()->whereIn('group_chapter_id', $lesson_vocabulary)->pluck('vocabulary_id');

        $vocabulary_list = Vocabulary::vocabularyactive(true)->with('levels')->whereIn('id', $vocabulary_id)->paginate($paging);

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
}
