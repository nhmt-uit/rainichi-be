<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 14:00
 */

namespace App\Http\Controllers\API\Exercise;


use App\Http\Controllers\Controller;
use App\Models\Answer;
use App\Models\Chapter;
use App\Models\Question;
use App\Service\BaseResponse;
use App\Service\UploadService;
use App\Transformers\QuestionTransformer;
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
     * @var QuestionTransformer
     */
    private $questionTransformer;

    function __construct(Manager $fractal, QuestionTransformer $questionTransformer)
    {
        $this->fractal = $fractal;
        $this->questionTransformer = $questionTransformer;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(Request $request)
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
        $question_id = $request->route('id');
        $data_change = $request->except('_method');
        $question = Question::query()->where('id', $question_id)->first();
        if ($question) {
//            check media
            if ($request->hasFile('media')) {
                $old_media = $question->media;
                $media = UploadService::handleUploadFile($request->file('media'), Config('uploadpath.question_media_folder'));
                $data_change['media'] = $media;
                if ($old_media != null) {
                    UploadService::handleRemoveFile($old_media);
                }
            }
            //check image
            if ($request->hasFile('image')) {
                $old_image = $question->image;
                $image = UploadService::handleUploadFile($request->file('image'), Config('uploadpath.question_image_folder'));
                $data_change['image'] = $image;
                if ($old_image != null) {
                    UploadService::handleRemoveFile($old_image);
                }
            }
            if ($data_change['level_id'] == 0) {
                $data_change['level_id'] = null;
            }
            $data_change['updated_by'] = Auth::user()->id;
            $question->update($data_change);
            if ($request->has('translations')) {
                $language_keys = array_keys($data_change['translations']);
                foreach ($language_keys as $language) {
                    $question->translateOrNew($language)->name = $data_change['translations'][$language]['name'];
                    if (array_key_exists('description', $data_change['translations'][$language])) {
                        $question->translateOrNew($language)->description = $data_change['translations'][$language]['description'];
                    }
                }
            }
//             get answer list
            if ($request->has('answers')) {
                self::updateAnswer($data_change['answers'], $question->id);
            }
            if ($request->has('delete_id')) {
                self::deleteAnswer($data_change['delete_id']);
            }
            if ($question->save()) {
                return BaseResponse::customResponse(
                    'Update successfully',
                    $question,
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
                Config('error_constant.question.question_not_found'),
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
        $question = Question::query()->with(['levels', 'chapter', 'user', 'child_questions.answer', 'answer'])->find($id);
        if ($question) {
            $question = new Item($question, $this->questionTransformer);
            $question = $this->fractal->createData($question);
            return BaseResponse::customResponse(
                'Get by id successfully',
                $question->toArray()['data'],
                true,
                200,
                200,
                'Success'
            );

        } else {
            return BaseResponse::customResponse(
                'Question not found',
                [],
                false,
                Config('error_constant.question.question_not_found'),
                404,
                'NotFound'
            );
        }
    }

    /**
     * @param $answers
     * @param $question_id
     * @throws \Exception
     */
    public function updateAnswer($answers, $question_id)
    {
        foreach ($answers as $answer) {
            if ($answer['id'] == 0) {
                $answer['question_id'] = $question_id;
                Answer::query()->create($answer);
            } else {
                Answer::query()->find($answer['id'])->update($answer);
            }
        }
    }

    /**
     * @param $list_id
     * @throws \Exception
     */
    public function deleteAnswer($list_id)
    {
        foreach ($list_id as $id) {
            $answer = Answer::query()->find($id);
            if ($answer)
                $answer->delete();
        }
    }
}
