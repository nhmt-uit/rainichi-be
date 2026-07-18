<?php
/**
 * Created by PhpStorm.
 * User: lecongthang
 * Date: 11/12/2018
 * Time: 09:47
 */

namespace App\Transformers;

use App\Models\GroupChapter;
use App\Models\QuestionGroup;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class GroupChapterTransformer extends TransformerAbstract
{

    public function transform(GroupChapter $group_chapter)
    {
        $groupInfo = QuestionGroup::getInfo([$group_chapter->id]);
        return [
            'id' => $group_chapter->id,
            'group_chapter_id' => $group_chapter->id,
            'name' => $group_chapter->name,
            'level_id' => $group_chapter->level_id,
            'level' => $group_chapter->levels->name,
            'chapter_id' => $group_chapter->chapter_id,
            'chapter_type' => $group_chapter->chapter->type,
            'is_active' => $group_chapter->is_active,
            'chapter' => [
                'name' => $group_chapter->chapter->name,
                'translations' => $group_chapter->chapter->getTranslationsArray()],
            'group_chapter' => [
                'translations' => $group_chapter->getTranslationsArray()
            ],
            'created_at' => Carbon::parse($group_chapter->created_at)->format('d-m-Y'),
            'created_by' => $group_chapter->createdBy->name,
            'translations' => $group_chapter->getTranslationsArray(),
            'total_score' => $groupInfo['total_score'],
            'total_question' => $groupInfo['total_question'],
        ];
    }





}
