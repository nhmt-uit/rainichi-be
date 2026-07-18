<?php

namespace App\Http\Controllers\API\Reading;

use App\Http\Controllers\Controller;
use App\Models\Reading;
use App\Models\ReadingQuestions;
use App\Service\BaseResponse;
use App\Transformers\ReadingTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class UpdateController extends Controller
{
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
    public function update(Request $request)
    {
        $reading_id = $request->route('id');
        $data_change = $request->except('_method');
        if ($request->get('level_id') == 0) {
            $data_change['level_id'] = null;
        }
        $reading = Reading::query()->find($reading_id);
        if ($reading) {
            if ($request->translations) {
                $language_keys = array_keys($data_change['translations']);
                foreach ($language_keys as $language) {
                    $reading->translateOrNew($language)->name = $data_change['translations'][$language]['name'];
                    $reading->translateOrNew($language)->description = $data_change['translations'][$language]['description'];
                }
            }
            if ($data_change['level_id'] == 0) {
                $data_change['level_id'] = null;
            }
            $data_change['updated_by'] = Auth::user()->id;
            $reading->update($data_change);
            if ($reading->save()) {
                if ($request->has('group_chapter_id')) {
                    $reading->groupReading()->sync([$request->get('group_chapter_id')]);
                }
                if ($request->has('question_id')) {
                    $question_id = $request->get('question_id');
                    ReadingQuestions::query()->where('reading_id', $reading->id)->delete();
                    foreach ($question_id as $q) {
                        ReadingQuestions::query()->create([
                            'question_id' => $q,
                            'reading_id' => $reading->id,
                            'is_active' => true
                        ]);
                    }
                }
                return BaseResponse::customResponse(
                    'Update successfully',
                    $reading,
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
                Config('error_constant.reading.not_found'),
                404,
                'NotFound'
            );
        }

    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function detail($id)
    {
        $reading = Reading::query()->with('levels', 'user', 'questions.answer')->find($id);
        if ($reading) {
            $reading = new Item($reading, $this->readingTransformer);
            $reading = $this->fractal->createData($reading);
            return BaseResponse::customResponse(
                'Get detail successfully',
                $reading->toArray()['data'],
                true,
                200,
                200,
                'Success'
            );

        } else {
            return BaseResponse::customResponse(
                'Reading not found',
                [],
                false,
                Config('error_constant.reading.not_found'),
                404,
                'NotFound'
            );
        }
    }

    public function listQuestionInReading($id)
    {
        $reading = Reading::query()->with('questions.answer')->find($id);
        if ($reading) {
            return BaseResponse::customResponse(
                'Get detail successfully',
                $reading->questions,
                true,
                200,
                200,
                'Success'
            );

        } else {
            return BaseResponse::customResponse(
                'Reading not found',
                [],
                false,
                Config('error_constant.reading.not_found'),
                404,
                'NotFound'
            );
        }
    }
}
