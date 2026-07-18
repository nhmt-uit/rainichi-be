<?php
/**
 * Created by PhpStorm.
 * User: tuduong
 * Date: 08/01/2019
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Grammar\Sentence;


use App\Http\Controllers\Controller;
use App\Models\Grammar;
use App\Models\GrammarSentence;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateController extends Controller
{
    /**
     * Update sentence
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $grammar_id = $request->route('grammar_id');
        $sentence_id = $request->route('id');
        $data_change = $request->except('_method');
        $sentence = GrammarSentence::query()
            ->where('id', $sentence_id)
            ->where('grammar_id', $grammar_id)
            ->first();

        if ($sentence) {
            if ($request->translations) {
                $language_keys = array_keys($data_change['translations']);
                foreach ($language_keys as $language) {
                    $sentence->translateOrNew($language)->name = $data_change['translations'][$language]['name'];
                    $sentence->translateOrNew($language)->content = $data_change['translations'][$language]['content'];
                    $sentence->translateOrNew($language)->example = $data_change['translations'][$language]['example'];
                }
            }

            $sentence->update($data_change);

            if ($sentence->save()) {
                return BaseResponse::customResponse(
                    'Update successfully',
                    $sentence,
                    true,
                    200,
                    200,
                    'Success'
                );
            }
        } else {
            return BaseResponse::customResponse(
                'sentence not found',
                [],
                false,
                Config('error_constant.grammarSentence.sentence_not_found'),
                404,
                'NotFound'
            );
        }
    }

}
