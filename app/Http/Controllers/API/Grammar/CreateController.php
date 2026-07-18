<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Grammar;


use App\Http\Controllers\Controller;
use App\Models\Grammar;
use App\Models\GrammarGroup;
use App\Models\GrammarSentence;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{
    /**
     * Create new grammar
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $grammar_data = $request->all();
        $grammar_data['created_by'] = Auth::user()->id;
        if ($grammar_data['level_id'] == 0) {
            $grammar_data['level_id'] = null;
        }
        $grammar = Grammar::query()->create($grammar_data);
        // get translation keys and add to translation table
        $language_keys = array_keys($grammar_data['translations']);
        foreach ($language_keys as $language) {
            $grammar->translateOrNew($language)->name = $grammar_data['translations'][$language]['name'];
            $grammar->translateOrNew($language)->description = $grammar_data['translations'][$language]['description'];
            $grammar->translateOrNew($language)->example = $grammar_data['translations'][$language]['example'];
        }
        if ($grammar->save()) {
            if ($request->has('group_chapter_id')) {
                $grammar->groupGrammar()->sync([$request->get('group_chapter_id')]);
            }
            return BaseResponse::customResponse(
                'Create successfully',
                $grammar,
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
                Config('error_constant.grammar.insert_fail'),
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
        $conversation = $request->get('grammar_id');
        foreach ($conversation as $c) {
            $data = GrammarGroup::query()->where('grammar_id', $c)->where('group_chapter_id', $request->route('group_id'))->first();
            if (!$data) {
                GrammarGroup::query()->create([
                    'grammar_id' => $c,
                    'group_chapter_id' => $request->route('group_id')
                ]);
            }
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
