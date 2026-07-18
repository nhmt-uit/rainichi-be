<?php

namespace App\Http\Controllers\API\GroupChapter;

use App\Http\Controllers\Controller;
use App\Models\GroupChapter;
use App\Models\Vocabulary;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $group_chapter_id = $request->route('id');
        $data_change = $request->except('_method');
        $group_chapter = GroupChapter::find($group_chapter_id);
        if ($group_chapter) {
            if ($request->translations) {
                $language_keys = array_keys($data_change['translations']);
                foreach ($language_keys as $language) {
                    $group_chapter->translateOrNew($language)->name = $data_change['translations'][$language]['name'];
                }
            }
            $group_chapter->update($data_change);
            if ($group_chapter->save()) {
                return BaseResponse::customResponse(
                    'Update successfully',
                    $group_chapter,
                    true,
                    200,
                    200,
                    'Success'
                );
            }
        } else {
            return BaseResponse::customResponse(
                'Group skill not found',
                [],
                false,
                Config('error_constant.vocabulary.not_found'),
                404,
                'NotFound'
            );
        }

    }

    public function detail($id)
    {
        $group_chapter = GroupChapter::query()->find($id);
        if ($group_chapter) {
            return BaseResponse::customResponse(
                'Update successfully',
                $group_chapter,
                true,
                200,
                200,
                'Success'
            );
        } else {
            return BaseResponse::customResponse(
                'Group skill not found',
                [],
                false,
                Config('error_constant.vocabulary.not_found'),
                404,
                'NotFound'
            );
        }
    }
}
