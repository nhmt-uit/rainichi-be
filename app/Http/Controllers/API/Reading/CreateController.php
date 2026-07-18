<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 03/12/2018
 * Time: 10:28
 */

namespace App\Http\Controllers\API\Reading;


use App\Http\Controllers\Controller;
use App\Models\Reading;
use App\Models\ReadingGroup;
use App\Models\ReadingQuestions;
use App\Service\BaseResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{

    /**
     * Add new Reading.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $reading_data = $request->all();
        $reading_data['created_by'] = Auth::user()->id;
        if ($reading_data['level_id'] == 0) {
            $reading_data['level_id'] = null;
        }
        $reading = Reading::create($reading_data);

        // get translation keys and add to translation table
        $language_keys = array_keys($reading_data['translations']);
        foreach ($language_keys as $language) {
            $reading->translateOrNew($language)->name = $reading_data['translations'][$language]['name'];
            $reading->translateOrNew($language)->description = $reading_data['translations'][$language]['description'];
        }
        if ($reading->save()) {
            if ($request->has('group_chapter_id')) {
                $reading->groupReading()->sync([$request->get('group_chapter_id')]);
            }
            if ($request->has('question_id')) {
                $question_id = $request->get('question_id');
                foreach ($question_id as $q) {
                    ReadingQuestions::query()->create([
                        'question_id' => $q,
                        'reading_id' => $reading->id,
                        'is_active' => true
                    ]);
                }
            }
            return BaseResponse::customResponse(
                'Create successfully',
                $reading,
                true,
                200,
                201,
                'Created'
            );
        } else {
            return BaseResponse::customResponse(
                'Fail to insert',
                [],
                false,
                Config('error_constant.reading.insert_fail'),
                422,
                'Unprocessable Entity'
            );
        }

    }

    /**
     * Add conversation to group
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToGroup(Request $request)
    {
        $reading_ids = $request->get('reading_id');
        foreach ($reading_ids as $r) {
            ReadingGroup::query()->create([
                'reading_id' => $r,
                'group_chapter_id' => $request->route('group_id')
            ]);
        }
        return BaseResponse::customResponse(
            'Create successfully',
            [],
            true,
            200,
            201,
            'Created'
        );
    }
}
