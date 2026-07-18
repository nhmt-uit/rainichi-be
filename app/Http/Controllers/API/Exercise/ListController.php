<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Exercise;


use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\LessonVocabulary;
use App\Models\Question;
use App\Models\QuestionGroup;
use App\Service\BaseResponse;
use App\Transformers\QuestionTransformer;
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
     * @var QuestionTransformer
     */
    private $questionTransformer;

    function __construct(Manager $fractal, QuestionTransformer $questionTransformer)
    {
        $this->fractal = $fractal;
        $this->questionTransformer = $questionTransformer;
    }

    public function index(Request $request)
    {
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $created_by = $request->query('created_by');
        $levels = $request->query('levels');
        $column = $request->query('column');
        $category = $request->query('category');
        $is_skill = $request->query('is_skill');
        $order_by_type = $request->query('order_by_type');
        $search_string = $request->query('search_string');
        $lang = $request->query('lang');
        $question_list = Question::with(['levels', 'chapter', 'user', 'child_questions'])->where('hidden_in_list', false)
            ->searchBy($search_string, $lang)
            ->searchByCreatedBy($created_by)
            ->searchByLevel($levels)
            ->searchByCategory($category)
            ->searchByListening($is_skill)
            ->orderByCustom($column, $order_by_type)
            ->orderByName($column, $order_by_type, $lang)
            ->paginate($paging);

        $questions = new Collection($question_list->items(), $this->questionTransformer);
        $questions->setPaginator(new IlluminatePaginatorAdapter($question_list));
        $questions = $this->fractal->createData($questions);
        return BaseResponse::customResponse(
            'Get list successfully',
            $questions->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $questions->toArray()['meta']
        );
    }


    /**
     * @param Request $request
     * @param $lesson_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListInLesson(Request $request, $lesson_id)
    {
        switch ($request->segment(2)) {
            case 'exercise':
                $type = Chapter::EXERCISE;
                break;
            case 'listening':
                $type = Chapter::LISTENING;
                break;
            case 'reading':
                $type = Chapter::READING;
                break;
            default:
                $type = Chapter::EXERCISE;
        }
        $group_chapter_id = LessonVocabulary::query()->where('lesson_id', $lesson_id)->pluck('group_chapter_id');
        if ($group_chapter_id) {
            $question_id = null;
            $paging = $request->query('per_page') ? $request->query('per_page') : 15;
            if ($type == Chapter::READING || Chapter::LISTENING) {
                $question_id = QuestionGroup::query()->whereHas('groupChapter', function ($q) use ($type) {
                    $q->where('chapter_id', $type);
                })->whereIn('group_chapter_id', $group_chapter_id)->pluck('question_id');
            } else {
                $question_id = QuestionGroup::query()->whereIn('group_chapter_id', $group_chapter_id)->pluck('question_id');
            }
            $question_data = Question::query()
                ->with('child_questions.answer', 'answer')
                ->whereIn('id', $question_id)
                ->where('is_active', true);
            $total = array_sum(array_column($question_data->get()->toArray(), 'total_question'));
            $question= $question_data->paginate($paging);

            $questions = new Collection($question->items(), $this->questionTransformer);
            $questions->setPaginator(new IlluminatePaginatorAdapter($question));
            $questions = $this->fractal->createData($questions);
            $meta = $questions->toArray()['meta'];
            $meta['total_question'] = $total;
            return BaseResponse::customResponse(
                'Get list successfully',
                $questions->toArray()['data'],
                true,
                200,
                200,
                'Success',
                $meta
            );
        }


    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListInGroup(Request $request)
    {
        switch ($request->segment(2)) {
            case 'exercise':
                $type = Chapter::EXERCISE;
                break;
            case 'listening':
                $type = Chapter::LISTENING;
                break;
            case 'reading':
                $type = Chapter::READING;
                break;
            default:
                $type = Chapter::EXERCISE;
        }
        $question_id = QuestionGroup::query()->whereHas('groupChapter', function ($q) use ($type) {
            $q->where('chapter_id', $type);
        })->where('group_chapter_id', $request->route('group_id'))->pluck('question_id');
        $paging = $request->query('per_page') ? $request->query('per_page') : 15;
        $created_by = $request->query('created_by');
        $levels = $request->query('levels');
        $column = $request->query('column');
        $order_by_type = $request->query('order_by_type');
        $question_list = Question::query()->where('hidden_in_list', false)
            ->whereIn('id', $question_id)
            ->with('levels', 'user')
            ->searchByCreatedBy($created_by)
            ->searchByLevel($levels)
            ->orderByCustom($column, $order_by_type)
            ->paginate($paging);
        $questions = new Collection($question_list->items(), $this->questionTransformer);
        $questions->setPaginator(new IlluminatePaginatorAdapter($question_list));
        $questions = $this->fractal->createData($questions);
        return BaseResponse::customResponse(
            'Get list successfully',
            $questions->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $questions->toArray()['meta']
        );
    }
}
