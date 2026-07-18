<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Listening;


use App\Http\Controllers\Controller;
use App\Models\Listening;
use App\Models\Question;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;

class CreateController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $listening_data = $request->all();

        $question_data = $listening_data['question'];

        if ($request->hasFile('audio')) {

            $audio = UploadService::handleUploadFile($request->file('audio'), Config('uploadpath.question_media_folder'));
            $question_data['media'] = $audio;
        }

        $question = Question::query()->create($question_data);
        if($question){
            $listening_data['question_id'] = $question->id;
            $listening = Listening::query()->create($listening_data);

            return BaseResponse::customResponse(
                'Create successfully',
                $listening,
                true,
                200, 201,
                "Created",
                []
            );
        }
    }
}
