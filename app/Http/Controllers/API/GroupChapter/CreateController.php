<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 03/12/2018
 * Time: 10:28
 */

namespace App\Http\Controllers\API\GroupChapter;


use App\Http\Controllers\Controller;
use App\Models\GroupChapter;
use App\Models\LessonVocabulary;
use App\Models\VocabularyGroup;
use App\Service\BaseResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{

    /**
     * Add new vocabulary.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $group_chapter_data = $request->all();
        $group_chapter_data['created_by'] = Auth::user()->id;
        $group_chapter = GroupChapter::create($group_chapter_data);

        // get translation keys and add to translation table
        $language_keys = array_keys($group_chapter_data['translations']);
        foreach ($language_keys as $language) {
            $group_chapter->translateOrNew($language)->name = $group_chapter_data['translations'][$language]['name'];
        }
        if ($group_chapter->save()) {
            if ($request->has('lesson_id')) {
                $group_chapter->lessonChapterVocabulary()->sync($request->get('lesson_id'));
            }
            return BaseResponse::customResponse(
                'Create successfully',
                $group_chapter,
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
                Config('error_constant.vocabulary.insert_fail'),
                422,
                'Unprocessable Entity'
            );
        }

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addVocabulary(Request $request)
    {
        $vocabulary = $request->get('vocabulary_id');
        foreach ($vocabulary as $v) {
            VocabularyGroup::query()->create([
                'vocabulary_id' => $v,
                'group_chapter_id' => $request->route('id')
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

    public function addToLesson(Request $request)
    {
        $group_chapter_id = $request->get('group_chapter_id');
        foreach ($group_chapter_id as $v) {
            LessonVocabulary::query()->create([
                'lesson_id' => $request->route('id'),
                'group_chapter_id' => $v
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
