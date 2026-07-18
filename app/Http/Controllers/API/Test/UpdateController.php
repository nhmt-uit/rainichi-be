<?php

namespace App\Http\Controllers\API\Test;

use App\Http\Controllers\Controller;
use App\Models\CourseTest;
use App\Models\Question;
use App\Models\QuestionGroup;
use App\Models\Test;
use App\Models\TestChapter;
use App\Models\TestFail;
use App\Models\TestQuestions;
use App\Models\TestTimes;
use App\Models\Vocabulary;
use App\Service\BaseResponse;
use App\Service\UploadService;
use App\Transformers\QuestionTransformer;
use App\Transformers\TestAdminTransformer;
use App\Transformers\VocabularyAdminTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;

class UpdateController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    /**
     * @var TestAdminTransformer
     */
    private $testAdminTransformer;

    /**
     * @var QuestionTransformer
     */
    private $questionTransformer;


    function __construct(Manager $fractal, TestAdminTransformer $testAdminTransformer, QuestionTransformer $questionTransformer)
    {
        $this->fractal = $fractal;
        $this->testAdminTransformer = $testAdminTransformer;
        $this->questionTransformer = $questionTransformer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {

        $test_id = $request->route('id');
        $data_change = $request->except('_method');
        if ($data_change['level_id'] == 0) {
            $data_change['level_id'] = null;
        }
        $test = Test::find($test_id);
        if ($test) {
            if ($request->translations) {
                $language_keys = array_keys($data_change['translations']);
                foreach ($language_keys as $language) {
                    $test->translateOrNew($language)->name = $data_change['translations'][$language]['name'];
                    $test->translateOrNew($language)->description = $data_change['translations'][$language]['description'];
                }
            }
            if ($request->hasFile('video')) {
                $old_video = $test->video;
                $video = UploadService::handleUploadFile($request->file('video'), Config('uploadpath.test_folder'));
                $data_change['video'] = $video;
                if ($old_video != null) {
                    UploadService::handleRemoveFile($old_video);
                }
            }
            if ($request->hasFile('image')) {
                $old_image = $test->image;
                $image = UploadService::handleUploadFile($request->file('image'), Config('uploadpath.test_images_folder'));
                $data_change['image'] = $image;
                if ($old_image != null) {
                    UploadService::handleRemoveFile($old_image);
                }
            }
            $data_change['updated_by'] = Auth::user()->id;
            $test->update($data_change);
            if ($test->save()) {
                if ($request->has('test_time')) {
                    $test_time = $data_change['test_time'];
                    $chapter_ids = []; #for update
                    foreach ($test_time as $t) {
                        $t['test_id'] = $test->id;
                        if ($t['id'] == 0) {
                            TestTimes::query()->create($t);
                            if ($t['chapter_id']) {
                                self::createChapterInExam($t['chapter_id'], $test->id);
                            }
                        } else {
                            TestTimes::query()->find($t['id'])->update($t);
                        }
                        $chapter_ids = array_merge($chapter_ids, explode(",", $t['chapter_id']));
                    }
                    if (sizeof($chapter_ids)) {
                        self::updateChapterInExam($chapter_ids, $test->id);
                    }

                }
                if ($request->has('test_time_deleted')) {
                    TestTimes::query()->whereIn('id', $data_change['test_time_deleted'])->delete();
                }
                if ($request->has('test_fail')) {
                    $test_fail = $data_change['test_fail'];
                    foreach ($test_fail as $tf) {
                        $tf['test_id'] = $test->id;
                        if ($tf['id'] == 0) {
                            TestFail::query()->create($tf);
                        } else {
                            TestFail::query()->find($tf['id'])->update($tf);

                        }
                    }
                }
                if ($request->has('test_fail_deleted')) {
                    TestFail::query()->whereIn('id', $data_change['test_fail_deleted'])->delete();
                }

//                if ($request->has('question_id')) {
//                    $question_id = $request->get('question_id');
//                    foreach ($question_id as $q) {
//                        TestQuestions::query()->create([
//                            'question_id' => $q,
//                            'test_id' => $test->id,
//                            'is_active' => true
//                        ]);
//                    }
//                }
//                if ($request->has('question_deleted')) {
//                    TestQuestions::query()->whereIn('id', $data_change['question_deleted'])->delete();
//                }
                return BaseResponse::customResponse(
                    'Update successfully',
                    $test,
                    true,
                    200,
                    200,
                    'Success'
                );
            }


        } else {
            return BaseResponse::customResponse(
                'Vocabulary not found',
                [],
                false,
                Config('error_constant.test.not_found'),
                404,
                'NotFound'
            );
        }

    }

    /**
     * @param $list_chapter
     * @param $test_id
     */
    public function createChapterInExam($list_chapter, $test_id)
    {
        $chapter = (explode(",", $list_chapter));
        foreach ($chapter as $c) {
            TestChapter::query()->create([
                'test_id' => $test_id,
                'chapter_id' => $c
            ]);
        }
    }

    /**
     * @param array $new_chapter_ids
     * @param $test_id
     */
    public function updateChapterInExam($new_chapter_ids, $test_id)
    {

        $old_chapter_ids = TestChapter::query()->where('test_id', $test_id)->pluck('chapter_id')->toArray();
        if ($delete_ids = array_diff($old_chapter_ids, $new_chapter_ids)) {
            Log::debug('chapter id will delete : ' . json_encode($delete_ids));
            TestChapter::query()->where('test_id', $test_id)->whereIn('chapter_id', $delete_ids)->delete();
        }
        if ($new_ids = array_diff($new_chapter_ids, $old_chapter_ids)) {
            foreach ($new_ids as $c) {
                TestChapter::query()->create([
                    'test_id' => $test_id,
                    'chapter_id' => $c
                ]);
            }
        }

    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail($id)
    {
        $test = Test::query()->with(['testTimes', 'testFail', 'levels', 'user'])->find($id);
        if ($test) {
            $test = new Item($test, $this->testAdminTransformer);
            $test = $this->fractal->createData($test);
            return BaseResponse::customResponse(
                'Get detail successfully',
                $test->toArray()['data'],
                true,
                200,
                200,
                'Success'
            );

        } else {
            return BaseResponse::customResponse(
                'Test not found',
                [],
                false,
                Config('error_constant.vocabulary.not_found'),
                404,
                'NotFound'
            );
        }
    }


    /**
     * @param $test_time_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getQuestionByTestTimeId($test_time_id)
    {
        $question = [];
        $meta = [];
        $testTime = TestTimes::query()->find($test_time_id);
        if ($testTime) {
            $group_chapter_id = TestQuestions::query()->where('test_time_id', $test_time_id)->pluck('group_chapter_id');
            $question_id = QuestionGroup::query()->whereIn('group_chapter_id', $group_chapter_id)->pluck('question_id');
            $questionsData = Question::query()->with(['child_questions.answer', 'answer'])->whereIn('id', $question_id)->get();
            $totalQuestion = array_sum(array_column($questionsData->toArray(), 'total_question'));
            $totalScore = array_sum(array_column($questionsData->toArray(), 'total_score'));
            $question = new Collection($questionsData, $this->questionTransformer);
            $question = $this->fractal->createData($question);
            $meta['total_time'] = (int)$testTime->time;
            $meta['total_question'] = $totalQuestion;
            $meta['total_score'] = round($totalScore);
        }

        return BaseResponse::customResponse(
            'Get by id successfully',
            $question->toArray()['data'],
            true,
            200,
            200,
            'Success',
            $meta
        );
    }

    /**
     * Update multiple sort order lesson in course.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSortInCourse(Request $request)
    {
        $sort = $request->all();
        $course_id = $request->route('course_id');
        foreach ($sort as $s) {
            CourseTest::query()->where('course_id', $course_id)->where('test_id', $s['test_id'])->update(['sort_order' => $s['sort_order']]);
        }
        return BaseResponse::customResponse(
            'Update success',
            [],
            true,
            200,
            200,
            'Success'
        );
    }
}
