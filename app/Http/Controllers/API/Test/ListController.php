<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 03/12/2018
 * Time: 11:50
 */

namespace App\Http\Controllers\API\Test;


use App\Http\Controllers\Controller;
use App\Models\CourseTest;
use App\Models\GroupChapter;
use App\Models\QuestionGroup;
use App\Models\Test;
use App\Models\TestChapter;
use App\Models\TestQuestions;
use App\Models\TestTimes;
use App\Service\BaseResponse;
use App\Transformers\GroupChapterTransformer;
use App\Transformers\TestAdminTransformer;
use App\Transformers\TestTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Resource\Collection;

class ListController extends Controller
{
    const COMBO = 2;
    const SINGLE = 1;
    /**
     * @var Manager
     */
    private $fractal;
    /**
     * @var TestAdminTransformer
     */
    private $testAdminTransformer;
    /**
     * @var TestTransformer
     */
    private $testTransformer;
    /**
     * @var GroupChapterTransformer
     */
    private $groupChapterTransformer;

    function __construct(Manager $fractal, TestAdminTransformer $testAdminTransformer, GroupChapterTransformer $groupChapterTransformer, TestTransformer $testTransformer)
    {
        $this->fractal = $fractal;
        $this->testAdminTransformer = $testAdminTransformer;
        $this->testTransformer = $testTransformer;
        $this->groupChapterTransformer = $groupChapterTransformer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getListAdmin(Request $request)
    {
        $test_id_list = null;
        $course_id = $request->query('course_id');
        if (isset($course_id)) {
            $test_id_list = CourseTest::query()->where('course_id', $course_id)->orderBy('course_test.sort_order')->pluck('test_id');
        }
        $per_page = $request->get('per_page') ? $request->get('per_page') : 15;
        $level_id = $request->query('levels');
        $test_id = $request->query('test_id');
        $created_by = $request->query('created_by');
        $search_string = $request->query('search_string');
        $lang = $request->query('lang');
        $column = $request->query('column') ?? 'test.sort_order';
        $is_cms = $request->query('is_cms');
        $order_by_type = $request->query('order_by_type') ?? 'desc';
        $chapter_id = $request->query('chapter_id');
        $type = $request->query('type');
        $course_type_id = $request->query('course_type_id');
        $customer_type_id = $request->query('customer_type_id');
        $is_shop = $request->get("is_shop");
        $test_list = Test::with(['user', 'testTimes', 'testFail', 'chapters'])
            ->getByLevel($level_id)
            ->getActive($is_shop)
            ->getByUser($created_by)
            ->searchString($search_string, $lang)
            ->orderByName($column, $order_by_type, $lang)
            ->getByCourseId($test_id_list, $course_id, $is_cms)
            ->getByChapterId($chapter_id)
            ->getByType($type)
            ->getByCourseType($course_type_id)
            ->getByTestId($test_id)
            ->orderByCustom($column, $order_by_type)
            ->getCustomerType($customer_type_id)
            ->paginate($per_page);
        $tests = new Collection($test_list, $this->testAdminTransformer);
        $tests->setPaginator(new IlluminatePaginatorAdapter($test_list));
        $tests = $this->fractal->createData($tests);
        return BaseResponse::customResponse(
            'Success',
            $tests->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $tests->toArray()['meta']
        );
    }

    /**
     * List test for web and mobile Test Screen
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) {
        $test_id_list = null;
        $per_page = $request->get('per_page') ? $request->get('per_page') : 15;
        $course_id = $request->query('course_id');
        if (isset($course_id)) {
            $test_id_list = CourseTest::query()->where('course_id', $course_id)->orderBy('sort_order')->pluck('test_id');
        }
        $type = $request->query('type');
        $test_id = $request->query('test_id');
        $level_id = $request->query('levels');
        $course_type_id = $request->query('course_type_id');
        $customer_type_id = $request->query('customer_type_id');
        $test_list = Test::query()
            ->where("test.is_active", true)
            ->orderByCourse($test_id_list, $course_id)
            ->getCustomerType($customer_type_id)
            ->getByCourseType($course_type_id)
            ->getByLevel($level_id)
            ->getByTestId($test_id)
            ->getByType($type)
            ->paginate($per_page);
        $tests = new Collection($test_list, $this->testTransformer);
        $tests->setPaginator(new IlluminatePaginatorAdapter($test_list));
        $tests = $this->fractal->createData($tests);
        return BaseResponse::customResponse(
            'Success',
            $tests->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $tests->toArray()['meta']
        );
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getGroupInTestSection(Request $request)
    {
        $per_page = $request->get('per_page') ? $request->get('per_page') : 15;
        $test_id = $request->route('test_id');
        $chapter_id = $request->query('chapter_id');
        $test_chapter = TestChapter::query()->where('test_id', $test_id)->where('chapter_id', $chapter_id)->pluck('id');
        $test_questions = TestQuestions::query()->whereIn('test_chapter_id', $test_chapter)->get();
        $group_chapter_id = $test_questions ? $test_questions->pluck('group_chapter_id') : [];
        $groups = GroupChapter::query()->whereIn('id', $group_chapter_id)->paginate($per_page);
        $group = new Collection($groups, $this->groupChapterTransformer);
        $group->setPaginator(new IlluminatePaginatorAdapter($groups));
        $group = $this->fractal->createData($group);
        $data = $group->toArray()['data'];

        return BaseResponse::customResponse(
            'Success',
            $data,
            true,
            200,
            200,
            'Success',
            $group->toArray()['meta']
        );
    }
}
