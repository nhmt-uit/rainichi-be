<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 05/12/2018
 * Time: 13:58
 */

namespace App\Http\Controllers\API\Kanji;


use App\Http\Controllers\Controller;
use App\Http\Requests\KanjiRequest;
use App\Models\Kanji;
use App\Models\KanjiGroup;
use App\Service\BaseResponse;
use App\Service\UploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreateController extends Controller
{
    public function create(KanjiRequest $request)
    {
        $kanji_data = $request->all();
        //check audio file
        if ($request->hasFile('audio')) {

            $audio = UploadService::handleUploadFile($request->file('audio'), Config('uploadpath.kanji_audio_folder'));
            $kanji_data['audio'] = $audio;
        }
        $kanji_data['created_by'] = Auth::user()->id;
        if ($kanji_data['level_id'] == 0) {
            $kanji_data['level_id'] = null;
        }
        $kanji = Kanji::create($kanji_data);

        // get translation keys and add to translation table
        $language_keys = array_keys($kanji_data['translations']);
        foreach ($language_keys as $language) {
            $kanji->translateOrNew($language)->meaning = $kanji_data['translations'][$language]['meaning'];
            $kanji->translateOrNew($language)->chinese_vietnamese_word = $kanji_data['translations'][$language]['chinese_vietnamese_word'];
            $kanji->translateOrNew($language)->example1 = $kanji_data['translations'][$language]['example1'];
            $kanji->translateOrNew($language)->example2 = $kanji_data['translations'][$language]['example2'];
        }
        if ($kanji->save()) {
            if ($request->has('group_chapter_id')) {
                $kanji->groupKanji()->sync([$request->get('group_chapter_id')]);
            }
            return BaseResponse::customResponse(
                'Create successfully',
                $kanji,
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
     * Add kanji to group
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addToGroup(Request $request)
    {
        $kanji = $request->get('kanji_id');
        foreach ($kanji as $k) {
            $data = KanjiGroup::query()->where('kanji_id', $k)->where('group_chapter_id', $request->route('group_id'))->first();
            if (!$data) {
                KanjiGroup::query()->create([
                    'kanji_id' => $k,
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
