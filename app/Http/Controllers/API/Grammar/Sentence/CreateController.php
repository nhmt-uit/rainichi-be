<?php
/**
 * Created by PhpStorm.
 * User: tuduong
 * Date: 08/01/2019
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Grammar\Sentence;


use App\Http\Controllers\Controller;
use App\Models\GrammarSentence;
use App\Service\BaseResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{
    /**
     * Create new grammar sentences
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $sentence_data = $request->all();
        $sentence_data['created_by'] = Auth::user()->id;
        $sentence = GrammarSentence::query()->create($sentence_data);
        $sentence['grammar_id'] = $request->route('grammar_id');

        // get translation keys and add to translation table
        $language_keys = array_keys($sentence_data['translations']);
        foreach ($language_keys as $language) {
            $sentence->translateOrNew($language)->name = $sentence_data['translations'][$language]['name'];
            $sentence->translateOrNew($language)->content = $sentence_data['translations'][$language]['content'];
            $sentence->translateOrNew($language)->example = $sentence_data['translations'][$language]['example'];
        }
        if ($sentence->save()) {
            return BaseResponse::customResponse(
                'Create successfully',
                $sentence,
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
                Config('error_constant.grammarSentence.sentence_insert_fail'),
                422,
                'Unprocessable Entity'
            );
        }
    }
}
