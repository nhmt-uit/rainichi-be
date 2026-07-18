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
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateController extends Controller
{
    /**
     * Update grammar
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $grammar_id = $request->route('id');
        $data_change = $request->except('_method');
        if (array_key_exists('level_id', $data_change)) {
            if ($data_change['level_id'] == 0) $data_change['level_id'] = null;
        }
        $grammar = Grammar::query()->find($grammar_id);
        if ($grammar) {
            if ($request->translations) {
                $language_keys = array_keys($data_change['translations']);
                foreach ($language_keys as $language) {
                    $grammar->translateOrNew($language)->name = $data_change['translations'][$language]['name'];
                    $grammar->translateOrNew($language)->description = $data_change['translations'][$language]['description'];
                    $grammar->translateOrNew($language)->example = $data_change['translations'][$language]['example'];
                }
            }
            $grammar['updated_by'] = Auth::user()->id;
            $grammar->update($data_change);
            if ($grammar->save()) {
                $grammar->video = media_url_web($grammar->video);
                return BaseResponse::customResponse(
                    'Update successfully',
                    $grammar,
                    true,
                    200,
                    200,
                    'Success'
                );
            }
        } else {
            return BaseResponse::customResponse(
                'grammar not found',
                [],
                false,
                Config('error_constant.grammar.grammar_not_found'),
                404,
                'NotFound'
            );
        }
    }

}
